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

const aptitudeForm = usePrecognitionForm("post", route("aptitudes.storeAptitude"), {
    aptitude_description: '',
    course: '',

});
const criteriaForm = usePrecognitionForm("post", route("aptitudes.storeCriteria"), {
    criteria_description: '',
    aptitude: '',
});

const updateAptitudeForm = (aptitude) => usePrecognitionForm("put", route("aptitudes.updateAptitude", { aptitude }), {
    aptitude_description: '',
    aptitude: '',
});

/*aptitudeForm.setValidationTimeout(300);
criteriaForm.setValidationTimeout(300);*/
/*const form = usePrecognitionForm("post", route("aptitudes.store"), {
    aptitude: '',
    aptitude_description: '',
    course: '',
    criteria_description: ''
});*/

const selectedSection = ref(null);

const handleSectionChange = (event) => {
    const newSectionId = event.target.value;
    aptitudeForm.course = '';
};

const aptitudeFormSubmitted = ref(false);

const submitAptitude = () => {
    if (aptitudeForm.aptitude_description && aptitudeForm.aptitude_description !== '') {
        const selectedCourse = aptitudeForm.course;

        aptitudeForm.submit({
            preserveScroll: true,
            onSuccess: () => {
                aptitudeForm.reset();
                aptitudeForm.course = selectedCourse;
                aptitudeFormSubmitted.value = false;

            },
            onError: (errors) => {
                console.error(errors);
            }
        });
    } else {
        //console.error("Aptitude description cannot be empty.");
        aptitudeFormSubmitted.value = true;
    }
};

const newCriteria = ref({});
const criteriaFormSubmitted = ref({});

const submitCriteria = (aptitudeId) => {
    const criteriaDescription = newCriteria.value[aptitudeId];
    if (criteriaDescription && criteriaDescription.trim() !== '') {
        criteriaForm.criteria_description = criteriaDescription;
        criteriaForm.aptitude = aptitudeId;
        criteriaForm.submit({
            preserveScroll: true,
            onSuccess: () => {
                newCriteria.value[aptitudeId] = '';
                criteriaFormSubmitted.value[aptitudeId] = false;

            },
            onError: (errors) => {
                console.error(errors);
            }
        });
    } else {
        //console.error("Criteria description cannot be empty.");
        criteriaFormSubmitted.value[aptitudeId] = true;
        //console.log("criteriaFormSubmitted", criteriaFormSubmitted.value);
    }

};
const toggleEditMode = (aptitude) => {
    aptitude.editMode = true;
    aptitude.updatedDescription = aptitude.description;
};

const cancelEditMode = (aptitude) => {
    aptitude.editMode = false;
};

const updateAptitude = (aptitude) => {
    const form = updateAptitudeForm(aptitude); // Dynamically create form with aptitude
    form.aptitude_description = aptitude.updatedDescription;
    form.aptitude = aptitude.id;

    form.submit({
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            aptitude.editMode = false;
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};



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
        <select name="course" id="course" v-model="aptitudeForm.course">
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

        <div v-if="aptitudeForm.course !== ''">
            <form @submit.prevent="submitAptitude">
                <label for="aptitude">Nouvel AA:</label><br>
                <textarea id="aptitude" v-model="aptitudeForm.aptitude_description"></textarea>
                <div v-if="aptitudeFormSubmitted && aptitudeForm.aptitude_description.trim() === ''">
                    L'AA ne peut pas être vide.
                </div><br>
                <button type="submit" class="bg-green-500 hover:bg-green-600 p-2">Ajouter l'AA</button>
            </form>
        </div>

        <div
            v-if="aptitudeForm.course && aptitudesByCourses[aptitudeForm.course] && aptitudesByCourses[aptitudeForm.course].length">
            <h2 class="font-bold">Liste des AA du cours</h2>
            <ul v-for="(aptitude, index) in aptitudesByCourses[aptitudeForm.course]" :key="aptitude.id">
                <br><br>
                <li v-if="!aptitude.editMode">
                    AA {{ index + 1 }}: <br> {{ aptitude.description }}
                    <br><br>
                    <button @click="toggleEditMode(aptitude)">Edit</button>
                </li>
                <li v-else>
                    <form @submit.prevent="updateAptitude(aptitude)">
                        <input type="text" v-model="aptitude.updatedDescription">
                        <button type="submit">Save</button>
                        <button @click="cancelEditMode(aptitude)">Cancel</button>
                    </form>
                </li>
                <form @submit.prevent="submitCriteria(aptitude.id)">
                    <label for="criteria">Nouveau critère:</label><br>
                    <textarea id="criteria" v-model="newCriteria[aptitude.id]"></textarea><br>
                    <div
                        v-if="criteriaFormSubmitted[aptitude.id] && (!newCriteria[aptitude.id] || !newCriteria[aptitude.id].trim())">
                        Le critère ne peut pas être vide.
                    </div><br>

                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 p-2">Ajouter le critère</button>
                </form>
                <li>
                    Critères de l'AA : <br><br>
                    <span v-for="(criteria, index) in criteriasByAptitudes[aptitude.id]" :key="criteria.id"> Critère
                        {{
                            index + 1 }}: {{ criteria.description }}<br><br></span>
                    --------------------<br>
                </li>
            </ul>
        </div>
        <div v-else-if="aptitudeForm.course == '' && !aptitudesByCourses[aptitudeForm.course]">
            <p>Veuillez choisir un cours</p>
        </div>
        <div v-else-if="aptitudeForm.course !== ''">
            <p>Pas encore d'AA pour ce cours</p>
        </div>
    </div>
    <!-- {{ aptitudeForm.errors }}-->
    <!--{{ criteriaForm }} -->
</template>