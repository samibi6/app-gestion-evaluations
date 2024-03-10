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
            denied_exam_date: '',
            denied_blunder_1: false,
            denied_blunder_1_justification: '',
            denied_blunder_2: false,
            denied_blunder_2_justification: '',
            denied_blunder_3: false,
            denied_blunder_4: false,
            denied_blunder_5: false,
            denied_justification_global: ''
        };
    } else {
        return {
            is_determining: !!studentData.is_determining,
            is_other: !!studentData.is_other,
            denied_exam_date: studentData.denied_exam_date || '',
            denied_blunder_1: !!studentData.denied_blunder_1,
            denied_blunder_1_justification: studentData.denied_blunder_1_justification || '',
            denied_blunder_2: !!studentData.denied_blunder_2,
            denied_blunder_2_justification: studentData.denied_blunder_2_justification || '',
            denied_blunder_3: !!studentData.denied_blunder_3,
            denied_blunder_4: !!studentData.denied_blunder_4,
            denied_blunder_5: !!studentData.denied_blunder_5,
            denied_justification_global: studentData.denied_justification_global || ''
        };
    }
};

const form = usePrecognitionForm("post", route("evals.storeDenied", {
    studentId: props.student,
    sectionId: props.section,
    courseId: props.course
}), initializeFormData());

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
    <AppLayout>
        <div class="p-5 shadow-md mt-12">
            <h2 class="text-2xl font-bold text-center">Refus de</h2>
            <h1 class="mt-5 mb-4 font-bold text-3xl underline text-center">{{ student.first_name + " " + student.last_name
            }} -
                {{
                    course.name }} - {{ section.name }}
            </h1>
        </div>
        <form @submit.prevent="submit" class="max-w-lg mx-auto mt-8 mb-12 border border-gray-400 rounded-lg">
            <div class="bg-white overflow-hidden shadow-xl p-6">
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
                            <input type="date" v-model="form.denied_exam_date" :max="formattedDate"
                                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                            <div v-if="form.invalid('denied_exam_date')" class="text-sm text-red-600">
                                {{ form.errors.denied_exam_date }}
                            </div>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.denied_blunder_1" class="form-checkbox">
                            <span class="ml-2">Fraude, plagiat ou non-citation des sources dans une épreuve certificative de
                                2ème session</span>
                        </label>
                        <div v-if="form.denied_blunder_1" class="ml-8 mt-4">
                            <label class="text-gray-700" for="justification_blunder_1">Justification et explication
                                :</label>
                            <textarea placeholder="Justification" id="justification_blunder_1"
                                v-model="form.denied_blunder_1_justification" rows="4" cols="50"
                                class="bg-gray-200 min-h-36 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"></textarea>
                            <div v-if="form.invalid('denied_blunder_1_justification')" class="text-sm text-red-600">
                                {{ form.errors.denied_blunder_1_justification }}
                            </div>
                        </div>
                    </li>
                    <li class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.denied_blunder_2" class="form-checkbox">
                            <span class="ml-2">Récidive de fraude, plagiat ou non-citation des sources dans une épreuve
                                certificative de 1ère session</span>
                        </label>
                        <div v-if="form.denied_blunder_2" class="ml-8 mt-4">
                            <label class="text-gray-700" for="justification_blunder_2">Justification et explication
                                :</label>
                            <textarea placeholder="Justification" id="justification_blunder_2"
                                v-model="form.denied_blunder_2_justification" rows="4" cols="50"
                                class="bg-gray-200 min-h-36 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"></textarea>
                            <div v-if="form.invalid('denied_blunder_2_justification')" class="text-sm text-red-600">
                                {{ form.errors.denied_blunder_2_justification }}
                            </div>
                        </div>
                    </li>
                    <li class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.denied_blunder_3" class="form-checkbox">
                            <span class="ml-2">Absence(s) à l'(aux) épreuve(s) de 2ème session</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.denied_blunder_4" class="form-checkbox">
                            <span class="ml-2">Non-justification de l'absence à l'épreuve de 1ère session</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label class="inline-flex items-center">
                            <input type="checkbox" v-model="form.denied_blunder_5" class="form-checkbox">
                            <span class="ml-2">Acquis d'apprentissage non atteint(s)</span>
                        </label>
                    </li>
                    <li class="mb-4">
                        <label class="block text-gray-700">Justification et explication globale :</label>
                        <textarea placeholder="Justification" v-model="form.denied_justification_global" rows="4" cols="50"
                            class="bg-gray-200 min-h-40 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"></textarea>
                        <div v-if="form.invalid('denied_justification_global')" class="text-sm text-red-600">
                            {{ form.errors.denied_justification_global }}
                        </div>
                    </li>
                </ul>
                <button :disabled="form.processing" type="submit"
                    class="mt-6 focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                    Refuser
                </button>
                <a class="cursor-pointer  font-bold text-red-500 hover:bg-red-500 hover:text-white transition inline-block px-4 py-1.5 ml-3 border border-red-500 rounded-md"
                    :href="route('evals.fail', { courseId: course.id, studentId: student.id, sectionId: section })">Retour</a>
            </div>
        </form>
    </AppLayout>
</template>

  