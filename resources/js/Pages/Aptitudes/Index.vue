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

const props = defineProps(['sections', 'courses', 'coursesBySections', 'aptitudesByCourses', 'criteriasByAptitudes', 'proficienciesByCourses']);

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
    console.log("Selected course ID:", aptitudeForm.course);
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
    const CritUpdform = updateCriteriaForm(criteria);
    CritUpdform.criteria_description = criteria.updatedDescription;
    CritUpdform.criteria = criteria.id;

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

const proficiencyForm = usePrecognitionForm("post", route("aptitudes.storeProficiency"), {
    criteria_skill: '',
    indicator: '',
    course: '',
});

const proficiencyFormSubmitted = ref(false);

const submitProficiency = () => {

    if (proficiencyForm.criteria_skill && proficiencyForm.criteria_skill.trim() !== '') {


        const selectedCourse = aptitudeForm.course;

        proficiencyForm.course = selectedCourse;


        proficiencyForm.submit({
            preserveScroll: true,
            onSuccess: () => {

                proficiencyForm.criteria_skill = '';
                proficiencyForm.indicator = '';


                proficiencyFormSubmitted.value = false;
            },
            onError: (errors) => {
                console.error(errors);
            }
        });
    } else {

        proficiencyFormSubmitted.value = true;
    }
};

const updateProficiencyForm = (proficiency) => usePrecognitionForm("patch", route("aptitudes.updateProficiency", { proficiency }), {
    criteria_skill: proficiency.criteria_skill,
    indicator: proficiency.indicator,
});

const toggleProficiencyEditMode = (proficiency) => {
    proficiency.editMode = true;
    proficiency.updatedCriteriaSkill = proficiency.criteria_skill;
    proficiency.updatedIndicator = proficiency.indicator;
};


const cancelProficiencyEditMode = (proficiency) => {
    proficiency.editMode = false;
};

const updateProficiency = (proficiency) => {
    const proficiencyUpdateForm = updateProficiencyForm(proficiency);
    proficiencyUpdateForm.criteria_skill = proficiency.updatedCriteriaSkill;
    proficiencyUpdateForm.indicator = proficiency.updatedIndicator;

    proficiencyUpdateForm.submit({
        preserveScroll: true,
        onSuccess: () => {
            proficiency.editMode = false;
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

const confirmingProficiencyDeletion = ref(false);
const proficiencyIdToDelete = ref(null);
const formDeleteProficiency = useForm("delete", {});

const confirmProficiencyDeletion = (proficiencyId) => {
    proficiencyIdToDelete.value = proficiencyId;
    confirmingProficiencyDeletion.value = true;
};

const closeProficiencyModal = () => {
    confirmingProficiencyDeletion.value = false;
};

const deleteProficiency = () => {
    formDeleteProficiency.delete(route("aptitudes.deleteProficiency", proficiencyIdToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingProficiencyDeletion.value = false;
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

const editCourseLead = ref();
const updatedCourseLead = ref();

const toggleEditCourseLeadMode = (courses) => {
    const courseId = aptitudeForm.course;
    const course = courses.find(course => course.id === courseId); // Find the course by ID
    if (!course) {
        console.error("Course not found or courseId is invalid:", courseId);
        return;
    }
    editCourseLead.value = true;
    updatedCourseLead.value = course.lead;
};

const cancelEditCourseLeadMode = () => {
    editCourseLead.value = false;
};

const updateCourseLead = (courses) => {
    const courseId = aptitudeForm.course;
    const course = courses.find(course => course.id === courseId); // Find the course by ID
    if (!course) {
        console.error("Course not found or courseId is invalid:", courseId);
        return;
    }

    const courseUpdateForm = usePrecognitionForm("patch", route("aptitudes.updateLead", { course }), {
        lead: updatedCourseLead.value,
        course: course.id,
    });

    courseUpdateForm.submit({
        preserveScroll: true,
        onSuccess: () => {
            course.lead = updatedCourseLead.value;
            editCourseLead.value = false;
        },
        onError: (errors) => {
            console.error(errors);
        }
    });
};

const findCourseLead = (courses, courseId) => {
    const course = courses.find(course => course.id === courseId);
    return course ? course.lead : 'Chapeau non trouvé';
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
            <div v-if="!editCourseLead">
                <p v-if="aptitudeForm.course !== ''">
                    Chapeau du cours : {{ findCourseLead(courses, aptitudeForm.course) }}
                </p>

                <button @click="toggleEditCourseLeadMode(courses)" class="bg-orange-500 hover:bg-orange-600">Edit
                </button>
            </div>
            <div v-else>
                <form @submit.prevent="updateCourseLead(courses)">
                    <label for="course_lead">Modifier le chapeau du cours:</label><br>
                    <textarea id="course_lead" v-model="updatedCourseLead"></textarea>
                    <button type="submit" class="bg-green-500 hover:bg-green-600">Save</button>
                    <button @click="cancelEditCourseLeadMode" class="bg-yellow-500 hover:bg-yellow-600">Cancel</button>
                </form>
            </div>
        </div>

        <br><br>
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
            <ul v-for="( aptitude, index ) in  aptitudesByCourses[aptitudeForm.course] " :key="aptitude.id">
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
                        <label for="aptitude_description-edit">Modifier l'AA:</label><br>
                        <textarea id="aptitude_description-edit" v-model="aptitude.updatedDescription"></textarea>
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
                    </div>

                    <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 p-2">Ajouter
                    </button>
                    <br><br>
                </form>
                Critères de réussite de l'AA : <br><br>
                <ul v-if="criteriasByAptitudes[aptitude.id] && criteriasByAptitudes[aptitude.id].length">
                    <li v-for="( criteria, index ) in  criteriasByAptitudes[aptitude.id] " :key="criteria.id">
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
                                <label for="criteria_description-edit">Modifier le critère de réussite:</label><br>
                                <textarea id="criteria_description-edit" v-model="criteria.updatedDescription"></textarea>
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

    <div v-if="aptitudeForm.course && proficienciesByCourses[aptitudeForm.course]">


        <form @submit.prevent="submitProficiency" v-if="aptitudeForm.course !== ''">
            <label for="criteria_skill">Nouveau critère de maitrise:</label><br>
            <textarea id="criteria_skill" v-model="proficiencyForm.criteria_skill"></textarea>
            <div v-if="proficiencyFormSubmitted && !proficiencyForm.criteria_skill.trim()">
                Le critère de maitrise est obligatoire
            </div>
            <br>

            <label for="indicator">Nouvel indicateur:</label><br>
            <textarea id="indicator" v-model="proficiencyForm.indicator"></textarea>
            <br>

            <button type="submit" class="bg-blue-500 hover:bg-blue-600 p-2">Ajouter
            </button>
        </form>
        <br><br>
        <h2 v-if="proficienciesByCourses[aptitudeForm.course].length > 0" class="font-bold">Liste des critères de degré de
            maitrise du cours</h2>
        <h2 v-else class="font-bold">Ce cours ne comporte pas encore de critères de degré de maitrise</h2>
        <br>

        <ul v-for="( proficiency, index ) in  proficienciesByCourses[aptitudeForm.course] " :key="proficiency.id">
            <li>
                <div v-if="!proficiency.editMode">
                    Critère de maîtrise {{ index + 1 }}: <br> {{ proficiency.criteria_skill }}
                </div>
                <div v-else>
                    <label for="criteria_skill-edit">Modifier le critère de maitrise</label><br>
                    <textarea id="criteria_skill-edit" v-model="proficiency.updatedCriteriaSkill"></textarea>
                </div>
            </li>
            <li>
                <div v-if="!proficiency.editMode">
                    <div> Indicateur de maîtrise {{ index + 1 }}: <br> {{ proficiency.indicator }}</div>
                    <button @click="toggleProficiencyEditMode(proficiency)"
                        class="bg-orange-500 hover:bg-orange-600">Edit</button>
                    <button @click="confirmProficiencyDeletion(proficiency.id)"
                        class="bg-red-500 hover:bg-red-600">Delete</button>
                </div>
                <div v-else>
                    <label for="indicator-edit">Modifier l'indicateur de maitrise</label><br>
                    <textarea id="indicator-edit" v-model="proficiency.updatedIndicator"></textarea>
                </div>
            </li>


            <div v-if="proficiency.editMode">
                <form @submit.prevent="updateProficiency(proficiency)">
                    <button type="submit" class="bg-green-500 hover:bg-green-600">Save</button>
                    <button type="button" class="bg-yellow-500 hover:bg-yellow-600"
                        @click="cancelProficiencyEditMode(proficiency)">Cancel</button>
                </form>
            </div>
            <br><br>
        </ul>

    </div>

    <DialogModal :show="confirmingProficiencyDeletion" @close="closeProficiencyModal">
        <template #title> Supprimer le critère de maitrise et son indicateur </template>
        <template #content>
            Êtes-vous sûr de vouloir supprimer ce critère de maitrise ainsi que son indicateur? Cette action est
            irréversible.
        </template>
        <template #footer>
            <SecondaryButton @click="closeProficiencyModal"> Annuler </SecondaryButton>
            <DangerButton class="ms-3" :class="{ 'opacity-25': confirmingProficiencyDeletion.processing }"
                :disabled="confirmingProficiencyDeletion.processing" @click="deleteProficiency">
                Supprimer
            </DangerButton>
        </template>
    </DialogModal>



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