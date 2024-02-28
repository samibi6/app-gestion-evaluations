<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['courseId', 'studentId', 'sectionId', 'next', 'criteriaData', 'criteriaByApt', 'aptitudes', 'acquired', 'section', 'proficiencies', 'acquiredProf', 'student', 'course']);

const criteriaForAptitudes = Object.fromEntries(
    Object.entries(props.criteriaByApt).filter(([aptitudeId]) => {
        return props.aptitudes.some(aptitude => aptitude.id === parseInt(aptitudeId));
    })
);

console.log(criteriaForAptitudes);

const form = usePrecognitionForm("post", route("evals.store", { studentId: props.studentId, sectionId: props.sectionId, courseId: props.courseId }), {
    criteria: Object.fromEntries(
        Object.keys(criteriaForAptitudes).flatMap(aptitudeId => {
            return criteriaForAptitudes[aptitudeId].map(criteria => [criteria.id, true]);
        })
    ),
    proficiency: Object.fromEntries(
        props.proficiencies.map(proficiency => [proficiency.id, 0])
    ),
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

</script>
<template>
    <div class="p-10">
        <ul>
            <li v-for="aptitude in aptitudes">{{ aptitude.description }}
                <ul>
                    <li v-for="criteria in criteriaByApt[aptitude.id]">
                        {{ criteria.description + " acquired ? -> " }}
                        {{ acquired[studentId][criteria.id] === 'undefined' ?
                            "c'est def" : "c'est pas def" }}</li>
                </ul>
            </li>
        </ul>
        <ul>
            <li v-for="proficiency in proficiencies">{{ proficiency.criteria_skill + " - " + proficiency.indicator }}
                {{ acquiredProf[studentId][proficiency.id] === 'undefined' ?
                    "c'est def" : "c'est pas def" }}</li>
        </ul>

        <h1>{{ student.first_name + " " + student.last_name }}</h1>


        <form @submit.prevent="submit">

            <table class="border m-auto">
                <tr class="border">
                    <th class="border">AA</th>
                    <th class="border w-[500px]">Critère</th>
                    <th class="border">Acquis</th>
                </tr>
                <tr v-for="aptitude in aptitudes" class="border">
                    <td class="border p-5">{{ aptitude.description }}</td>
                    <td class="border" colspan="2">
                        <table>
                            <tr class="border" v-for="criteria in criteriaByApt[aptitude.id]">
                                <td class="bg-red-500 p-5 w-[500px]">{{ criteria.description }}</td>
                                <td class="border p-5"><input type="checkbox" :name="'criteria_' + criteria.id"
                                        :id="criteria.id" v-model="form.criteria[criteria.id]"
                                        @change="console.log(form.criteria)"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>

            <table class="border m-auto w-[1000px]">
                <tr class="border">
                    <th class="border">Critère</th>
                    <th class="border w-[500px]">Indicateur</th>
                    <th class="border">Niveau de maitrise: 0 - 10</th>
                </tr>
                <tr v-for="proficiency in proficiencies" class="border">
                    <td class="border p-5">{{ proficiency.criteria_skill }}</td>
                    <td class="border p-5">{{ proficiency.indicator }}</td>
                    <td><input type="number" max="10" min="0" :name="'proficiency_' + proficiency.id" :id="proficiency.id"
                            v-model="form.proficiency[proficiency.id]" @change="console.log(form.proficiency)"></td>
                </tr>
            </table>

            <button type="submit" class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full block mx-auto">
                Sauvegarder l'évaluation
            </button>

        </form>
    </div>
</template>
