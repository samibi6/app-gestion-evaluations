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
    <AppLayout>
        <h1>Ajournement de </h1>
        <ul class="flex flex-wrap bg-zinc-300 w-fit p-5 m-2">
            <!-- <li class="bg-zinc-300 w-fit p-5 m-2">{{ section.id + ". " + section.name }}</li> -->
            <li>{{ student.last_name + ' ' + student.first_name }}
            <li>UE : {{ course.name }}</li>
            <li>Code de l'UE : {{ course.code }} </li>
            <li v-if="course.is_tfe" class="text-red-500 font-bold">
                Épreuve intégrée
            </li>

            </li>
            <div>
                <p>Année académique : {{ academicYear }}</p>
            </div>
        </ul>
        <form @submit.prevent="submit">
            <div>

                <ul>
                    <li>
                        <label>
                            <input type="checkbox" v-model="form.is_determining">UE déterminante</label>
                    </li>
                    <br>
                    <li>
                        <label>
                            <input type="checkbox" v-model="form.is_other">Autre</label>
                    </li>
                    <br>
                    <li>
                        <label>
                            Date de l'épreuve :
                            <input type="date" v-model="form.adjournment_exam_date" :max="formattedDate">
                            <div v-if="form.invalid('adjournment_exam_date')">
                                {{ form.errors.adjournment_exam_date }}
                            </div>
                        </label>
                    </li>
                    <br>
                    <li>
                        <label>
                            <input type="checkbox" v-model="form.adjournment_blunder_1">Fraude, plagiat ou non-citation des
                            sources dans une épreuve certificative</label>

                        <br>
                        <div v-if="form.adjournment_blunder_1">
                            <label for="justification">Justification et explication</label>
                            <br>
                            <textarea placeholder="Justification" id="justification"
                                v-model="form.adjournment_blunder_1_justification" rows="4" cols="50"></textarea>
                        </div>
                        <div v-if="form.invalid('adjournment_blunder_1_justification')">
                            {{ form.errors.adjournment_blunder_1_justification }}
                        </div>
                    </li>
                    <br>
                    <li>
                        <label>
                            <input type="checkbox" v-model="form.adjournment_blunder_2">Absence(s) valablement justifiée(s)
                            à l' (aux) épreuve(s) permettant de vérifier la maitrise des acquis d'apprentissage</label>
                    </li>
                </ul>
            </div>
            <br>
            <button :disabled="form.processing" type="submit" class="bg-red-500 hover:bg-red-600 p-2">Ajourner</button>
        </form>
    </AppLayout>
</template>