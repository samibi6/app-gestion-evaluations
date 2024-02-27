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

const updateCriteriaForm = (criteria) => usePrecognitionForm("put", route("aptitudes.updateCriteria", { criteria }), {
    criteria_description: '',
    criteria: '',
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
    const AptUpdform = updateAptitudeForm(aptitude);
    AptUpdform.aptitude_description = aptitude.updatedDescription;
    AptUpdform.aptitude = aptitude.id;

    AptUpdform.submit({
        preserveScroll: true,
        onSuccess: () => {
            AptUpdform.reset();
            aptitude.editMode = false;
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

const toggleCriteriaEditMode = (criteria) => {
    criteria.editMode = true;
    criteria.updatedDescription = criteria.description;
};

const cancelCriteriaEditMode = (criteria) => {
    criteria.editMode = false;
};

const updateCriteria = (criteria) => {
    const CritUpdform = updateCriteriaForm(criteria); // Use criteria.id instead of criteria
    CritUpdform.criteria_description = criteria.updatedDescription;
    CritUpdform.criteria = criteria.id; // Use CritUpdform.criteria instead of CritUpd.criteria

    CritUpdform.submit({
        preserveScroll: true,
        onSuccess: () => {
            CritUpdform.reset();
            criteria.editMode = false;
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};
/*placeholder delete logic*/
const confirmingAptitudeDeletion = ref(false);
const aptitudeIdToDelete = ref(null);
const formDeleteAptitude = useForm("delete", {});

const confirmAptitudeDeletion = (aptitudeId) => {
    aptitudeIdToDelete.value = aptitudeId;
    confirmingAptitudeDeletion.value = true;
};

const closeAptitudeModal = () => {
    confirmingAptitudeDeletion.value = false;
};

const deleteAptitude = () => {
    formDeleteAptitude.delete(route("aptitudes.deleteAptitude", aptitudeIdToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingAptitudeDeletion.value = false;
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

const confirmingCriteriaDeletion = ref(false);
const criteriaIdToDelete = ref(null);
const formDeleteCriteria = useForm("delete", {});

const confirmCriteriaDeletion = (criteriaId) => {
    criteriaIdToDelete.value = criteriaId;
    confirmingCriteriaDeletion.value = true;
};

const closeCriteriaModal = () => {
    confirmingCriteriaDeletion.value = false;
};

const deleteCriteria = () => {
    formDeleteCriteria.delete(route("aptitudes.deleteCriteria", criteriaIdToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingCriteriaDeletion.value = false;
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

                    <button @click="toggleEditMode(aptitude)" class="bg-orange-500 hover:bg-orange-600">Edit</button>
                    <button @click="confirmAptitudeDeletion(aptitude.id)"
                        class="bg-red-500 hover:bg-red-600">Delete</button>
                    <br><br>
                </li>
                <li v-else>
                    <form @submit.prevent="updateAptitude(aptitude)">
                        <input type="text" v-model="aptitude.updatedDescription">
                        <button type="submit" class="bg-green-500 hover:bg-green-600">Save</button>
                        <button @click="cancelEditMode(aptitude)" class="bg-yellow-500 hover:bg-yellow-600">Cancel</button>
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
                Critères de l'AA : <br><br>
                <ul v-if="criteriasByAptitudes[aptitude.id] && criteriasByAptitudes[aptitude.id].length">
                    <li v-for="(criteria, index) in criteriasByAptitudes[aptitude.id]" :key="criteria.id">
                        <div v-if="!criteria.editMode">
                            Critère {{ index + 1 }}: {{ criteria.description }}
                            <button @click="toggleCriteriaEditMode(criteria)"
                                class="bg-orange-500 hover:bg-orange-600">Edit</button>
                            <button @click="confirmCriteriaDeletion(criteria.id)"
                                class="bg-red-500 hover:bg-red-600">Delete</button>

                            <br><br>
                        </div>
                        <div v-else>
                            <form @submit.prevent="updateCriteria(criteria)">
                                <input type="text" v-model="criteria.updatedDescription">
                                <button type="submit" class="bg-green-500 hover:bg-green-600">Save</button>
                                <button type="button" class="bg-yellow-500 hover:bg-yellow-600"
                                    @click="cancelCriteriaEditMode(criteria)">Cancel</button>
                            </form>
                        </div>
                    </li>
                </ul>
            </ul>
        </div>
        <div v-else-if="aptitudeForm.course == '' && !aptitudesByCourses[aptitudeForm.course]">
            <p>Veuillez choisir un cours</p>
        </div>
        <div v-else-if="aptitudeForm.course !== ''">
            <p>Pas encore d'AA pour ce cours</p>
        </div>
    </div>
    <DialogModal :show="confirmingAptitudeDeletion" @close="closeAptitudeModal">
        <template #title> Supprimer l'AA </template>

        <template #content>
            Êtes-vous sûr de vouloir supprimer cet AA? Cette action est irréversible.
        </template>

        <template #footer>
            <SecondaryButton @click="closeAptitudeModal"> Annuler </SecondaryButton>

            <DangerButton class="ms-3" :class="{ 'opacity-25': confirmingAptitudeDeletion.processing }"
                :disabled="confirmingAptitudeDeletion.processing" @click="deleteAptitude">
                Supprimer
            </DangerButton>
        </template>
    </DialogModal>

    <DialogModal :show="confirmingCriteriaDeletion" @close="closeCriteriaModal">
        <template #title> Supprimer le critère </template>

        <template #content>
            Êtes-vous sûr de vouloir supprimer ce critère? Cette action est irréversible.
        </template>

        <template #footer>
            <SecondaryButton @click="closeCriteriaModal"> Annuler </SecondaryButton>

            <DangerButton class="ms-3" :class="{ 'opacity-25': confirmingCriteriaDeletion.processing }"
                :disabled="confirmingCriteriaDeletion.processing" @click="deleteCriteria">
                Supprimer
            </DangerButton>
        </template>
    </DialogModal>



    <!-- {{ aptitudeForm.errors }}-->
    <!--{{ criteriaForm }} -->
</template>