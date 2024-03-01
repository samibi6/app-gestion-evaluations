<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['course', 'section', 'student', 'courseStudent']);

const form = usePrecognitionForm("post", route("evals.storeAdjournment", { studentId: props.student, sectionId: props.section, courseId: props.course }), {
    is_determining: props.courseStudent.is_determining ? props.courseStudent.is_determining : false,
    is_other: props.courseStudent.is_other ? props.courseStudent.is_other : false,

    adjournment_exam_date: '',

    adjournment_blunder_1: false,
    adjournment_blunder_1_justification: '',

    adjournment_blunder_2: false,


});

form.setValidationTimeout(300);

const submit = () => {
    /* // Check if the selected date is in the future
     const selectedDate = new Date(form.adjournment_exam_date);
     const today = new Date();
     if (selectedDate > today) {
         // If the selected date is in the future, reset it to the current date
         form.adjournment_exam_date = '';
     }*/

    /* if (form.adjournment_blunder_1 && !form.adjournment_blunder_1_justification.trim()) {
         console.error('blunder_1 doit être coché pour remplir la justification');
         // Return to prevent form submission if the checkbox is checked but justification is empty
         return;
     }
 
     if (!form.adjournment_blunder_1) {
         // If blunder 1 is not checked, clear the justification
         form.adjournment_blunder_1_justification = '';
     }*/

    form.submit({
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};





const calculateAcademicYear = () => {
    const today = new Date();
    const currentYear = today.getFullYear();
    var academicYear = '';

    // Determine academic year based on current date
    if (today.getMonth() >= 8) { // September (index 8) or later
        academicYear = `${currentYear}-${currentYear + 1}`;
    } else { // Before September
        academicYear = `${currentYear - 1}-${currentYear}`;
    }

    return academicYear;
};

const academicYear = calculateAcademicYear();

const getCurrentDate = () => {
    const today = new Date();
    const year = today.getFullYear();
    let month = today.getMonth() + 1;
    if (month < 10) {
        month = '0' + month; // Add leading zero for single-digit months
    }
    let day = today.getDate();
    if (day < 10) {
        day = '0' + day; // Add leading zero for single-digit days
    }
    return `${year}-${month}-${day}`;
};
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
                            <input type="date" v-model="form.adjournment_exam_date" :max="getCurrentDate()">
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