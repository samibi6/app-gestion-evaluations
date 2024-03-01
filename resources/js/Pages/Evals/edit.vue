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

        <table class="mb-[200px] border border-gray-500 w-full">
            <tr class="border border-gray-500">
                <td class="border border-gray-500 w-[30%] p-5"><img src="/images/logoIfosup.svg" alt="" class="h-[100px]">
                </td>
                <td class="border border-gray-500 p-5">
                    <ul>
                        <li>Nom & prénom de l'étudiant.e :</li>
                        <li>Section :</li>
                        <li>Unité d'enseignement :</li>
                        <li>Code de l'UE :</li>
                        <li>Nom du/des chargés.es de cours :</li>
                        <li>Année scolaire :</li>
                    </ul>
                </td>
            </tr>
            <tr class="border border-gray-500">
                <td colspan="2" class="font-bold p-2">POUR ATTEINDRE LE SEUIL DE REUSSITE, L'ETUDIANT.E PROUVERA QU'IL/ELLE
                    EST
                    CAPABLE :</td>
            </tr>
            <tr class="border border-gray-500">
                <td class="border border-gray-500" colspan="3">
                    <table class="w-full">
                        <tr>
                            <td class="border border-gray-500" colspan="2">
                                (Indiquer ici le chapeau repris au-dessus des acquis d'apprentissage dans le dossier
                                pédagogique)
                            </td>
                            <td class="border border-gray-500 text-center w-[15%] p-2">ACQUIS</td>
                            <td class="border border-gray-500 border-collapse text-center w-[15%] p-2">NON ACQUIS</td>
                        </tr>
                        <!-- FOREACH AA -->
                        <tr>
                            <td class="border border-gray-500 w-[30%]">AA1: </td>
                            <td colspan="4">
                                <table class="w-full">
                                    <!-- FOREACH CRITERES -->
                                    <tr>
                                        <td class="border border-gray-500 w-[57%]">test</td>
                                        <td class="border border-gray-500 border-collapse text-center p-2 font-bold">X</td>
                                        <td class="border border-gray-500 text-center p-2 font-bold">X</td>
                                    </tr>
                                    <tr>
                                        <td class="border border-gray-500 w-[57%] font-bold text-center">Décision pour AA 1
                                            : tous les
                                            critères</td>
                                        <td class="border border-gray-500 text-center p-2 font-bold">X</td>
                                        <td class="border border-gray-500 text-center p-2 font-bold">X</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td class="border border-gray-500 w-[33%] text-center">Tous les AA sont " acquis " <br>
                    REUSSITE <br>
                    -> Voir degré de maitrise</td>
                <td class="border border-gray-500 w-[33%] text-center">Au moins un AA non-acquis (en 1ere session) <br>
                    AJOURNEMENT <br>
                    -> Voir justification ajournement</td>
                <td class="border border-gray-500 w-[33%] text-center">Au moins un AA non-acquis (en 2eme session) <br>
                    REUSSITE <br>
                    -> Voir justification de refus</td>
            </tr>
            <tr>
                <td colspan="3" class="font-bold text-center text-xl border-t-4 border-black">DEGRÉ DE MAÎTRISE (% ≥ 50)
                </td>
            </tr>
        </table>

        <a :href="route('pdf.success', { course: courseId, section: sectionId })">Générer pdf</a>

        <h1>{{ student.first_name + " " + student.last_name }}</h1>


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
                    <td><input type="number" max="10" min="0" :name="'proficiency_' + proficiency.id" :id="proficiency.id"
                            v-model="form.proficiency[proficiency.id]"></td>
                </tr>
            </table>

            <button type="submit" class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full block mx-auto">
                Sauvegarder l'évaluation
            </button>

        </form>
    </div>
</template>
