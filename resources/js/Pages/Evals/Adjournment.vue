<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['course', 'section', 'student', 'courseStudent']);

// Function to determine academic year based on current date
const calculateAcademicYear = () => {
    const today = new Date();
    const currentYear = today.getFullYear();
    const academicYearStartMonth = 8; // September (0-indexed)
    const academicYear = (today.getMonth() >= academicYearStartMonth) ?
        `${currentYear}-${currentYear + 1}` :
        `${currentYear - 1}-${currentYear}`;
    return academicYear;
};

const academicYear = calculateAcademicYear();

const getCurrentDate = () => {
    const today = new Date();
    const year = today.getFullYear();
    let month = today.getMonth() + 1;
    if (month < 10) month = '0' + month;
    let day = today.getDate();
    if (day < 10) day = '0' + day;
    return `${year}-${month}-${day}`;
};

const isInCurrentAcademicYear = (date_eval) => {
    const [startYear, endYear] = academicYear.split('-');
    const startDate = new Date(`${startYear}-09-01`);
    const endDate = new Date(`${endYear}-08-31`);
    const evalDate = new Date(date_eval);
    return evalDate >= startDate && evalDate <= endDate;
};

const initializeFormData = () => {
    const { date_eval, ...studentData } = props.courseStudent;
    if (!date_eval || !isInCurrentAcademicYear(date_eval)) {
        return {
            is_determining: false,
            is_other: false,
            adjournment_exam_date: '',
            adjournment_blunder_1: false,
            adjournment_blunder_1_justification: '',
            adjournment_blunder_2: false,
        };
    } else {
        return {
            is_determining: !!studentData.is_determining,
            is_other: !!studentData.is_other,
            adjournment_exam_date: studentData.adjournment_exam_date || '',
            adjournment_blunder_1: !!studentData.adjournment_blunder_1,
            adjournment_blunder_1_justification: studentData.adjournment_blunder_1_justification || '',
            adjournment_blunder_2: !!studentData.adjournment_blunder_2,
        };
    }
};

const form = usePrecognitionForm("post", route("evals.storeAdjournment", { studentId: props.student, sectionId: props.section, courseId: props.course }), initializeFormData());

form.setValidationTimeout(300);

const submit = () => {
    form.submit({
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};



const checkAndSetDate = (date_eval, academicYear) => {
    const currentDate = getCurrentDate();

    // Parse the academic year to compare it with date_eval
    const [startYear, endYear] = academicYear.split('-');
    const startDate = new Date(`${startYear}-09-01`);
    const endDate = new Date(`${endYear}-08-31`);

    // Parse the date_eval
    const evalDate = new Date(date_eval);

    // Check if date_eval falls outside the academic year
    if (evalDate < startDate || evalDate > endDate) {
        return currentDate; // Set formattedDate to today's date
    } else {
        // Format the date_eval to YYYY-MM-DD
        return `${evalDate.getFullYear()}-${(evalDate.getMonth() + 1).toString().padStart(2, '0')}-${evalDate.getDate().toString().padStart(2, '0')}`;
    }
};


const formattedDate = checkAndSetDate(props.courseStudent.date_eval, academicYear);


</script>

<template>
    <AppLayout title="Évaluations">
        <div class="p-5 shadow-md mt-12">
            <h2 class="text-2xl font-bold text-center">Ajournement de</h2>
            <h1 class="mt-5 mb-4 font-bold text-3xl underline text-center">{{ student.first_name + " " + student.last_name
            }} -
                {{
                    course.name }} - {{ section.name }}
            </h1>
        </div>

        <form @submit.prevent="submit" class="max-w-lg mx-auto mt-8 mb-12 border border-gray-400 rounded-lg">
            <div class="bg-white overflow-hidden shadow-xl  p-6">
                <ul>

                    <li class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.is_determining" class="form-checkbox">
                            <span class="ml-2">UE déterminante</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.is_other" class="form-checkbox">
                            <span class="ml-2">Autre</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label class="block text-gray-700">
                            Date de l'épreuve :
                            <input type="date" v-model="form.adjournment_exam_date" :max="formattedDate"
                                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                            <div v-if="form.invalid('adjournment_exam_date')" class="text-sm text-red-600">
                                {{ form.errors.adjournment_exam_date }}
                            </div>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.adjournment_blunder_1" class="form-checkbox">
                            <span class="ml-2">Fraude, plagiat ou non-citation des sources dans une épreuve
                                certificative</span>
                        </label>
                        <div v-if="form.adjournment_blunder_1" class="ml-8 mt-4">
                            <label class="text-gray-700" for="justification">Justification et explication :</label>
                            <textarea placeholder="Justification" id="justification"
                                v-model="form.adjournment_blunder_1_justification" rows="4" cols="50"
                                class="bg-gray-200 min-h-36 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"></textarea>
                            <div v-if="form.invalid('adjournment_blunder_1_justification')" class="text-sm text-red-600">
                                {{ form.errors.adjournment_blunder_1_justification }}
                            </div>
                        </div>
                    </li>
                    <li class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.adjournment_blunder_2" class="form-checkbox">
                            <span class="ml-2">Absence(s) valablement justifiée(s) à l' (aux) épreuve(s) permettant de
                                vérifier
                                la maitrise des acquis d'apprentissage</span>
                        </label>
                    </li>
                </ul>
                <button :disabled="form.processing" type="submit"
                    class="mt-6 focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Ajourner
                </button>
                <a class="cursor-pointer  font-bold text-red-500 hover:bg-red-500 hover:text-white transition inline-block px-4 py-1.5 ml-3 border border-red-500 rounded-md"
                    :href="route('evals.fail', { courseId: course.id, studentId: student.id, sectionId: section })">Retour</a>
            </div>
        </form>
    </AppLayout>
</template>