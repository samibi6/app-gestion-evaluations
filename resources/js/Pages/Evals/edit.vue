<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['courseId', 'studentId', 'sectionId', 'criteriaData', 'criteriaByApt', 'aptitudes', 'acquired', 'proficiencies', 'acquiredProf', 'student', 'course', 'courseStudentData', 'sectionData']);
// Function to determine academic year based on current date
// Function to determine academic year based on current date
function getCurrentAcademicYear() {
    const today = new Date();
    const currentMonth = today.getMonth(); // 0-indexed month

    // Assuming academic year starts in September and ends in August
    const academicYearStartMonth = 8; // September (0-indexed)
    const academicYearStartYear = (currentMonth >= academicYearStartMonth) ? today.getFullYear() : today.getFullYear() - 1;
    const academicYearEndYear = academicYearStartYear + 1;

    return {
        start: new Date(academicYearStartYear, academicYearStartMonth, 1),
        end: new Date(academicYearEndYear, academicYearStartMonth, 0)
    };
}

const currentAcademicYear = getCurrentAcademicYear();

const courseStudentDataDate = new Date(props.courseStudentData.date_eval);

// Function to check if a date falls within the current academic year
function isInCurrentAcademicYear(date) {
    return date >= currentAcademicYear.start && date <= currentAcademicYear.end;
}

// Check if date_eval is within the current academic year
const shouldPrefill = isInCurrentAcademicYear(courseStudentDataDate);

// For debugging purposes, log the current academic year and the evaluation date year
//console.log("Current Academic Year:", currentAcademicYear, "Evaluation Date:", courseStudentDataDate, "Should Prefill:", shouldPrefill);

let initialData = {};

// Conditionally prefill the data based on shouldPrefill
if (shouldPrefill) {
    if (props.acquired && props.acquired[props.studentId] !== undefined) {
        initialData.criteria = Object.entries(props.acquired[props.studentId]).reduce((acc, [criteriaId, value]) => {
            acc[criteriaId] = value === 1;
            return acc;
        }, {});
    }

    if (props.acquiredProf && props.acquiredProf[props.studentId] !== undefined) {
        initialData.proficiency = props.acquiredProf[props.studentId];
    }
}

const criteriaForAptitudes = Object.fromEntries(
    Object.entries(props.criteriaByApt).filter(([aptitudeId]) => {
        return props.aptitudes.some(aptitude => aptitude.id === parseInt(aptitudeId));
    })
);

const defaultCriteria = Object.fromEntries(
    Object.keys(criteriaForAptitudes).flatMap(aptitudeId => {
        return criteriaForAptitudes[aptitudeId].map(criteria => [criteria.id, true]);
    })
);

const defaultProficiency = Object.fromEntries(
    props.proficiencies.map(proficiency => [proficiency.id, 0])
);

// Conditionally set criteria and proficiency based on shouldPrefill
const criteria = shouldPrefill ? { ...defaultCriteria, ...initialData.criteria } : defaultCriteria;
const proficiency = shouldPrefill ? { ...defaultProficiency, ...initialData.proficiency } : defaultProficiency;


const form = usePrecognitionForm("post", route("evals.store", {
    studentId: props.studentId, courseId: props.courseId,
    sectionId: props.sectionId
}), {
    criteria,
    proficiency,
});

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
        // const { courseId, sectionId, studentId } = props;
        // const failureRoute = route('evals.fail', { courseId, sectionId, studentId });
        // return window.location.href = failureRoute;
    }
    form.submit({
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};


</script>

<template>
    <AppLayout title="Évaluations">
        <div class="p-10">
            <h2 class="text-2xl font-bold text-center mt-8">Évaluation de</h2>
            <h1 class="my-5 mb-20 font-bold text-3xl underline text-center">{{ student.first_name + " " + student.last_name
            }} -
                {{
                    course.name }} - {{ sectionData.name }}
            </h1>


            <form @submit.prevent="submit" class="p-10 border border-gray-400 bg-gray-300 rounded-md shadow-xl">
                <div class="text-red-500 font-bold" v-if="form.invalid('criteria')">
                    {{ form.errors.criteria }}
                </div>
                <a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition inline-block px-4 py-2 mb-5 border border-red-500 rounded-md"
                    :href="route('evals.show', { courseId: courseId, sectionId: sectionId })">Retour</a>

                <h2 class="text-center mb-8 text-xl font-bold">AA et critères de réussite</h2>
                <table class="mb-8 m-auto bg-indigo-200">
                    <tr class="bg-blue-200">
                        <th class="border border-black">AA</th>
                        <th class="border w-[500px] border-black">Critère</th>
                        <th class="border border-black">Acquis</th>
                    </tr>
                    <tr v-for="aptitude in aptitudes" class="">
                        <td class="border p-5 border-black">{{ aptitude.description }}</td>
                        <td class="border border-black" colspan="2">
                            <table>
                                <tr class="" v-for="criteria in criteriaByApt[aptitude.id]">
                                    <td class="p-5 w-[500px]">{{ criteria.description }}</td>
                                    <td class=" p-5"><input type="checkbox" :name="'criteria_' + criteria.id"
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
                    <h2 class="text-center mb-8 text-xl font-bold">Critères, indicateurs et niveaux de maitrise</h2>
                    <table class=" m-auto max-w-[1000px] mb-12 bg-indigo-200">
                        <tr class="bg-blue-200">
                            <th class="border border-black">Critère</th>
                            <th class="border border-black w-[500px]">Indicateur</th>
                            <th class="border border-black">Niveau de maitrise: 0 - 10</th>
                        </tr>
                        <tr v-for="proficiency in proficiencies" class="">
                            <td class="border border-black p-5">{{ proficiency.criteria_skill }}</td>
                            <td class="border border-black p-5">{{ proficiency.indicator }}</td>
                            <td class="text-center border border-black"><input type="number" max="10" min="0"
                                    :name="'proficiency_' + proficiency.id" :id="proficiency.id"
                                    v-model="form.proficiency[proficiency.id]"></td>
                        </tr>
                    </table>
                </div>

                <button type="submit"
                    class="text-center block mx-auto focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-lg px-6 py-3">
                    Enregistrer
                </button>

            </form>
        </div>
    </AppLayout>
</template>
