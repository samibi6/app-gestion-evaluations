<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['courseId', 'studentId', 'sectionId', 'next', 'criteriaData', 'criteriaByApt', 'aptitudes', 'acquired', 'proficiencies', 'acquiredProf', 'student', 'course']);

let initialData = {};

if (props.acquired && props.acquired[props.studentId] !== undefined) {
    initialData.criteria = Object.entries(props.acquired[props.studentId]).reduce((acc, [criteriaId, value]) => {
        acc[criteriaId] = value === 1;
        return acc;
    }, {});
}

if (props.acquiredProf && props.acquiredProf[props.studentId] !== undefined) {
    initialData.proficiency = props.acquiredProf[props.studentId];
}

console.log(initialData);

const criteriaForAptitudes = Object.fromEntries(
    Object.entries(props.criteriaByApt).filter(([aptitudeId]) => {
        return props.aptitudes.some(aptitude => aptitude.id === parseInt(aptitudeId));
    })
);

const form = usePrecognitionForm("post", route("evals.store", {
    studentId: props.studentId, courseId: props.courseId,
    sectionId: props.sectionId
}), {
    criteria: Object.fromEntries(
        Object.keys(criteriaForAptitudes).flatMap(aptitudeId => {
            return criteriaForAptitudes[aptitudeId].map(criteria => [criteria.id, true]);
        })
    ),
    proficiency: Object.fromEntries(
        props.proficiencies.map(proficiency => [proficiency.id, 0])
    ),
    ...initialData
});

console.log(form);

form.setValidationTimeout(300);

const areAllCriteriaChecked = () => {
    for (const criteria of Object.values(form.criteria)) {
        if (!criteria) {
            return false;
        }
    }
    return true;
};

const submit = () => {
    if (!areAllCriteriaChecked()) {
        for (const key in form.proficiency) {
            form.proficiency[key] = null;
        }
    }
    form.submit({
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

</script>

<template>
    <div class="p-10">
        <a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2 mb-5 border border-zinc-300"
            :href="route('evals.show', { courseId: courseId, sectionId: sectionId })">Retour</a>

        <h1 class="my-5 font-bold text-3xl underline text-center">{{ student.first_name + " " + student.last_name }}
        </h1>


        <form @submit.prevent="submit">
            <div class="text-red-500 font-bold" v-if="form.invalid('criteria')">
                {{ form.errors.criteria }}
            </div>
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
                                <td class="p-5 w-[500px]">{{ criteria.description }}</td>
                                <td class="border p-5"><input type="checkbox" :name="'criteria_' + criteria.id"
                                        :id="criteria.id" v-model="form.criteria[criteria.id]"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div v-if="areAllCriteriaChecked()">
                <div class="text-red-500 font-bold" v-if="form.invalid('proficiency')">
                    {{ form.errors.proficiency }}
                </div>
                <table class="border m-auto w-[1000px]">
                    <tr class="border">
                        <th class="border">Critère</th>
                        <th class="border w-[500px]">Indicateur</th>
                        <th class="border">Niveau de maitrise: 0 - 10</th>
                    </tr>
                    <tr v-for="proficiency in proficiencies" class="border">
                        <td class="border p-5">{{ proficiency.criteria_skill }}</td>
                        <td class="border p-5">{{ proficiency.indicator }}</td>
                        <td><input type="number" max="10" min="0" :name="'proficiency_' + proficiency.id"
                                :id="proficiency.id" v-model="form.proficiency[proficiency.id]"></td>
                    </tr>
                </table>
            </div>

            <button type="submit"
                class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full block mx-auto">
                Sauvegarder l'évaluation
            </button>

        </form>
    </div>
</template>
