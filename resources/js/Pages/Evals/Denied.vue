<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['course', 'section', 'student', 'courseStudent']);

const form = usePrecognitionForm("post", route("evals.storeDenied", { studentId: props.student, sectionId: props.section, courseId: props.course }), {
    is_determining: props.courseStudent.is_determining ? props.courseStudent.is_determining : false,
    is_other: props.courseStudent.is_other ? props.courseStudent.is_other : false,

    denied_exam_date: '',

    denied_blunder_1: false,
    denied_blunder_1_justification: '',

    denied_blunder_2: false,
    denied_blunder_2_justification: '',

    denied_blunder_3: false,

    denied_blunder_4: false,

    denied_blunder_5: false,

    denied_justification_global: '',


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
        <h1>Refus de </h1>
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
                            <input type="checkbox" v-model="form.is_determining" />UE déterminante</label>
                    </li>
                    <br>
                    <li>
                        <label>
                            <input type="checkbox" v-model="form.is_other" />Autre</label>
                    </li>
                    <br>
                    <li>
                        <label>
                            Date de l'épreuve :
                            <input type="date" v-model="form.denied_exam_date" :max="getCurrentDate()" />
                            <div v-if="form.invalid('denied_exam_date')">
                                {{ form.errors.denied_exam_date }}
                            </div>
                        </label>
                    </li>
                    <br>

                    <li>
                        <label>
                            <input type="checkbox" v-model="form.denied_blunder_1" />Fraude, plagiat ou non-citation des
                            sources dans une épreuve certificative de 2ème session</label>
                        <br>
                        <div v-if="form.denied_blunder_1">
                            <label for="justification_blunder_1">Justification et explication</label>
                            <br />
                            <textarea placeholder="Justification" id="justification_blunder_1"
                                v-model="form.denied_blunder_1_justification" rows="4" cols="50"></textarea>
                            <div v-if="form.invalid('denied_blunder_1_justification')">
                                {{ form.errors.denied_blunder_1_justification }}
                            </div>
                        </div>
                    </li>
                    <br>

                    <li>
                        <label>
                            <input type="checkbox" v-model="form.denied_blunder_2" />Récidive de fraude, plagiat ou
                            non-citation des sources dans une épreuve certificative de 1ère session</label>
                        <br />
                        <div v-if="form.denied_blunder_2">
                            <label for="justification_blunder_2">Justification et explication</label>
                            <br />
                            <textarea placeholder="Justification" id="justification_blunder_2"
                                v-model="form.denied_blunder_2_justification" rows="4" cols="50"></textarea>
                            <div v-if="form.invalid('denied_blunder_2_justification')">
                                {{ form.errors.denied_blunder_2_justification }}
                            </div>
                        </div>
                    </li>
                    <br>
                    <li>
                        <label>
                            <input type="checkbox" v-model="form.denied_blunder_3">Absence(s) à l'(aux) épreuve(s) de 2ème
                            session </label>
                    </li>
                    <br>
                    <li>
                        <label>
                            <input type="checkbox" v-model="form.denied_blunder_4">Non-justification de l'absence à
                            l'épreuve de 1ère session</label>
                    </li>
                    <br>
                    <li>
                        <label>
                            <input type="checkbox" v-model="form.denied_blunder_5">Acquis d'apprentissage non
                            atteint(s)</label>
                    </li>
                    <br><br>
                    <li>
                        <label>Justification et explication globale</label>
                        <br>
                        <textarea placeholder="Justification" v-model="form.denied_justification_global" rows="4"
                            cols="50"></textarea>

                        <div v-if="form.invalid('denied_justification_global')">
                            {{ form.errors.denied_justification_global }}
                        </div>
                    </li>
                </ul>
            </div>
            <button :disabled="form.processing" type="submit" class="bg-red-500 hover:bg-red-600 p-2">Refuser</button>
        </form>

    </AppLayout>
</template>
  