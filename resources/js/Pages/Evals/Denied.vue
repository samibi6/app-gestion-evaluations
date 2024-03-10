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
        <h1>Refus de </h1>
        <ul class="flex flex-col flex-wrap bg-zinc-300 w-fit p-5 m-2">

            <li>{{ student.last_name + ' ' + student.first_name }}</li>
            <li>UE : {{ course.name }}</li>
            <li>Code de l'UE : {{ course.code }} </li>
            <li>Section : {{ section.id + ". " + section.name }} </li>
            <li v-if="course.is_tfe" class="text-red-500 font-bold">
                Épreuve intégrée
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
                            <input type="date" v-model="form.denied_exam_date" :max="formattedDate" />
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

                        </div>
                        <div v-if="form.invalid('denied_blunder_1_justification')">
                            {{ form.errors.denied_blunder_1_justification }}
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

                        </div>
                        <div v-if="form.invalid('denied_blunder_2_justification')">
                            {{ form.errors.denied_blunder_2_justification }}
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
  