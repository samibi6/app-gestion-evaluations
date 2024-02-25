<script setup>
import ActionMessage from "@/Components/ActionMessage.vue";
import DangerButton from "@/Components/DangerButton.vue";
import DialogModal from "@/Components/DialogModal.vue";
import FormSection from "@/Components/FormSection.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import SectionTitle from "@/Components/SectionTitle.vue";
import TextInput from "@/Components/TextInput.vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import { useForm } from "@inertiajs/vue3";
import { ref, watch } from "vue";

const props = defineProps(['sections', 'courses', 'coursesBySections', 'aptitudesByCourses', 'criteriasByAptitudes']);

const form = usePrecognitionForm("post", route("aptitudes.store"), {
    aptitude: [],
    course: [],
    criteria: [],
});

form.setValidationTimeout(300);




const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});


const selectedSection = ref(null);

const handleSectionChange = (event) => {
    const newSectionId = event.target.value;
    form.course = [];

};

/*watch(() => form.course, (newValue) => {
    if (!newValue || !props.coursesBySections[newValue] || props.coursesBySections[newValue].length === 0) {
        form.course = '';
    }
});*/
/*console.log(props.aptitudesByCourses[10]);
console.log(props.criteriasByAptitudes[2]);*/
</script>
<template>
    <div>



        <label for="section">Section du cours</label>
        <select id="section" v-model="selectedSection" @change="handleSectionChange">
            <option :value="null">Toutes les sections</option>
            <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}</option>
        </select>
        <br><br>


        <label for="course">Cours</label>
        <select name="course" id="course" v-model="form.course">
            <option v-if="!selectedSection" v-for="course in courses" :key="course.id" :value="course.id">
                {{ course.name }}
            </option>
            <option v-else-if="coursesBySections[selectedSection]" v-for="course in coursesBySections[selectedSection]"
                :key="course.ids" :value="course.id">
                {{ course.name }}
            </option>
            <option v-else disabled>
                {{ selectedSection ? 'Aucun cours n\'est lié à cette section' : 'Veuillez d\'abord choisir une section' }}
            </option>
        </select>
        <br><br><br>

        <div v-if="form.course && aptitudesByCourses[form.course] && aptitudesByCourses[form.course].length">
            <h2 class="font-bold">Liste des AA du cours</h2>
            <ul v-for="(aptitude, index) in aptitudesByCourses[form.course]" :key="aptitude.id">

                <br><br>
                <li>
                    AA {{
                        index + 1 }}: <br> {{ aptitude.description }}
                    <br><br>

                </li>
                <li>
                    Critères de l'AA : <br><br>
                    <span v-for="(criteria, index) in criteriasByAptitudes[aptitude.id]" :key="criteria.id"> Critère {{
                        index + 1 }}: {{ criteria.description }}<br>
                        <br></span>
                    --------------------<br>
                </li>
            </ul>
        </div>
        <div v-else-if="form.course && !aptitudesByCourses[form.course]">

            <p>Veuillez choisir un cours</p>

        </div>

        <div v-else>

            <p>Pas encore d'AA pour ce cours</p>


        </div>
    </div>
</template>
