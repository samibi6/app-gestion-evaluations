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
    <AppLayout title="AA-Critères">

        <template #header>
            <h1 class="text-center font-bold text-2xl mt-12">
                Ajouter et modifier des AA, critères de réussite et degrés de maitrise
            </h1>
            <p class="text-center text-gray-700 text-lg mt-4">Ici vous pouvez consulter, ajouter, modifier et supprimer
                des
                acquis d'apprentissage, des critères de réussite ainsi que des critères de maitrise et leur indicateurs
                pour
                une UE donnée</p>
        </template>
        <div class="max-w-4xl mx-auto mt-8">
            <div class="bg-white overflow-hidden border border-gray-400 shadow-2xl rounded-lg p-6 mb-16 w-2/3 mx-auto">

                <div>
                    <label for="section" class="text-gray-700 block font-medium text-left">Section de l'UE</label>
                    <select id="section" v-model="selectedSection" @change="handleSectionChange"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full mb-4">
                        <option :value="null">Toutes les sections</option>
                        <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}
                        </option>
                    </select>
                </div>

                <div>
                    <label for="course" class="text-gray-700 block font-medium">Nom de l'UE</label>
                    <select name="course" id="course" v-model="aptitudeForm.course"
                        class="bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                        <option v-if="!selectedSection" v-for="course in courses" :key="course.id" :value="course.id">
                            {{ course.name }}
                        </option>
                        <option v-else-if="coursesBySections[selectedSection]"
                            v-for="course in coursesBySections[selectedSection]" :key="course.ids" :value="course.id">
                            {{ course.name }}
                        </option>
                        <option v-else disabled>
                            {{ selectedSection ? 'Aucune UE n\'est liée à cette section'
                        : 'Veuillez d\'abord choisir une section' }}
                        </option>
                    </select>

                </div>
            </div>

            <div class="mb-12">
                <div v-if="aptitudeForm.course !== ''">
                    <div v-if="!editCourseLead">
                        <p v-if="aptitudeForm.course !== ''" class="font-medium mb-2">Chapeau de l'UE :
                        <div class="text-gray-700 mt-4">{{
                        findCourseLead(courses, aptitudeForm.course) }}</div>
                        </p>
                        <button @click="toggleEditCourseLeadMode(courses)"
                            class="text-blue-600 hover:text-white border border-blue-600 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-2.5 py-2.5 mt-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                        </button>
                    </div>
                    <div v-else>
                        <form @submit.prevent="updateCourseLead(courses)" class="mt-4">
                            <label for="course_lead" class="font-medium mb-2 block">Modifier le chapeau de l'UE
                                :</label>
                            <textarea id="course_lead" v-model="updatedCourseLead"
                                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 min-h-40 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full mb-2"></textarea>
                            <button type="submit"
                                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 mt-2">Enregistrer</button>
                            <button @click="cancelEditCourseLeadMode"
                                class="focus:outline-none text-gray-700 bg-gray-300 shover:text-black border border-gray-700 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-1.5 ml-2">Annuler</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="border border-gray-400 shadow-2xl rounded-lg p-6 mb-8">
                <h2 class="font-bold text-xl">Acquis d'apprentissage et critères de réussite de l'UE </h2>

                <div class="mb-8" v-if="aptitudeForm.course !== ''">
                    <form @submit.prevent="submitAptitude" class="mt-4">
                        <label for="aptitude" class="text-gray-700 block font-medium">Nouvel AA :</label>
                        <textarea id="aptitude" v-model="aptitudeForm.aptitude_description"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 min-h-20 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full mb-2"></textarea>
                        <div v-if="aptitudeFormSubmitted && aptitudeForm.aptitude_description.trim() === ''"
                            class="text-sm text-red-600">L'AA ne peut pas être vide.</div>
                        <div>
                            <button type="submit"
                                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mt-2 block">Ajouter
                                l'AA</button>
                        </div>
                    </form>
                </div>



                <div v-if="aptitudeForm.course && aptitudesByCourses[aptitudeForm.course] && aptitudesByCourses[aptitudeForm.course].length"
                    class="mt-4">
                    <h2 class="text-lg font-semibold">Liste des AA de l'UE</h2>
                    <ul v-for="( aptitude, index ) in  aptitudesByCourses[aptitudeForm.course] " :key="aptitude.id">
                        <li class="mt-4 mb-8 border bg-gray-300 border-gray-400 rounded-lg p-6"
                            v-if="!aptitude.editMode">
                            <p class="font-semibold">AA {{ index + 1 }} :</p>
                            <p class="text-gray-700">{{ aptitude.description }}</p>
                            <button @click="toggleEditMode(aptitude)"
                                class="text-blue-600 hover:text-white border border-blue-600 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-2.5 py-2.5 mt-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                            </button>
                            <button type="button" @click="confirmAptitudeDeletion(aptitude.id)"
                                class="text-red-600 hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-2.5 py-2.5 ml-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </li>
                        <li v-else class="mt-4 mb-8">
                            <form @submit.prevent="updateAptitude(aptitude)">
                                <label for="aptitude_description-edit" class="font-semibold">Modifier
                                    l'AA :</label><br>
                                <textarea id="aptitude_description-edit" v-model="aptitude.updatedDescription"
                                    class="bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900  min-h-20 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"></textarea>
                                <div>
                                    <button type="submit"
                                        class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 mt-2">Enregistrer</button>
                                    <button type="button"
                                        class="focus:outline-none text-gray-700 bg-gray-300 shover:text-black border border-gray-700 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-1.5 ml-2"
                                        @click="cancelEditMode(aptitude)">Annuler</button>
                                </div>
                            </form>
                        </li>


                        <li class="mt-4 mb-8">
                            <form @submit.prevent="submitCriteria(aptitude.id)">
                                <label for="criteria" class="text-gray-700 block font-medium">Nouveau critère de
                                    réussite
                                    :</label>
                                <textarea id="criteria" v-model="newCriteria[aptitude.id]"
                                    class="bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 min-h-20 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"></textarea>
                                <div v-if="criteriaFormSubmitted[aptitude.id] && (!newCriteria[aptitude.id] || !newCriteria[aptitude.id].trim())"
                                    class="text-sm text-red-600 mt-2">Le critère ne peut pas être vide.</div>
                                <button type="submit"
                                    class="focus:outline-none text-white bg-purple-900 hover:bg-purple-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mt-2 block">Ajouter
                                    le critère</button>
                            </form>
                        </li>
                        <li class="mt-4">

                            <ul v-if="criteriasByAptitudes[aptitude.id] && criteriasByAptitudes[aptitude.id].length"
                                class="mt-2">
                                <p class="font-semibold text-lg">Critères de réussite de l'AA {{ index + 1 }} :</p>
                                <li v-for="( criteria, index ) in  criteriasByAptitudes[aptitude.id] "
                                    :key="criteria.id" class="mt-2">
                                    <div v-if="!criteria.editMode">
                                        <p class="">Critère {{ index + 1 }} :</p>
                                        <p class="text-gray-700">{{ criteria.description }}</p>
                                        <button @click="toggleCriteriaEditMode(criteria)"
                                            class="text-blue-600 hover:text-white border border-blue-600 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-2.5 py-2.5 mt-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>
                                        </button>
                                        <button type="button" @click="confirmCriteriaDeletion(criteria.id)"
                                            class="text-red-600 hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-2.5 py-2.5 ml-2">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                                xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd"
                                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    <div v-else class="mt-2">
                                        <form @submit.prevent="updateCriteria(criteria)">
                                            <label for="criteria_description-edit"
                                                class="text-gray-700 block font-medium">Modifier
                                                le
                                                critère de
                                                réussite:</label><br>
                                            <textarea id="criteria_description-edit"
                                                v-model="criteria.updatedDescription"
                                                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"></textarea>
                                            <button type="submit"
                                                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 mt-2">Enregistrer</button>
                                            <button type="button"
                                                class="focus:outline-none text-gray-700 bg-gray-300 shover:text-black border border-gray-700 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-1.5 ml-2"
                                                @click="cancelCriteriaEditMode(criteria)">Annuler</button>

                                        </form>
                                    </div>
                                </li>
                            </ul>
                            <ul v-else>Pas encore de critères de réussite pour cet AA</ul>
                        </li>
                    </ul>
                </div>
                <div v-else-if="aptitudeForm.course == '' && !aptitudesByCourses[aptitudeForm.course]" class="mt-4">
                    <p>Veuillez choisir une UE</p>
                </div>
                <div v-else-if="aptitudeForm.course !== ''" class="mt-4">
                    <p>Pas encore d'AA pour cette UE</p>
                </div>

            </div>






            <div v-if="aptitudeForm.course && proficienciesByCourses[aptitudeForm.course]"
                class="border border-gray-400 shadow-2xl rounded-lg p-6 mb-12 mt-8">
                <h2 class="font-bold text-xl mb-8">Critères et indicateurs de maitrise de l'UE</h2>
                <form @submit.prevent="submitProficiency" v-if="aptitudeForm.course !== ''">
                    <div class="mb-4">
                        <label for="criteria_skill" class="text-gray-700 block font-medium">Nouveau critère de
                            maitrise :</label>
                        <textarea id="criteria_skill" v-model="proficiencyForm.criteria_skill"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 min-h-20 focus:border-blue-500 p-2.5 w-full"></textarea>
                        <div v-if="proficiencyFormSubmitted && !proficiencyForm.criteria_skill.trim()"
                            class="text-sm text-red-600 mt-2">Le critère de maitrise est obligatoire</div>
                    </div>
                    <div class="mb-4">
                        <label for="indicator" class="text-gray-700 block font-medium">Nouvel indicateur :</label>
                        <textarea id="indicator" v-model="proficiencyForm.indicator"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 min-h-20 focus:border-blue-500 p-2.5 w-full"></textarea>
                    </div>
                    <button type="submit"
                        class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mt-2 block">Ajouter
                        le critère de maitrise </button>
                </form>

                <h2 v-if="proficienciesByCourses[aptitudeForm.course].length > 0" class="text-lg font-semibold mt-8">
                    Liste
                    des
                    critères de maitrise de l'UE</h2>
                <h2 v-else class="text-lg font-semibold mt-8">Cette UE ne comporte pas encore de critères de
                    maitrise
                </h2>

                <ul v-for="(proficiency, index) in proficienciesByCourses[aptitudeForm.course]" :key="proficiency.id"
                    class="mt-4">
                    <li class="mb-4">
                        <div v-if="!proficiency.editMode">
                            <p class="font-medium">Critère de maîtrise {{ index + 1 }} :</p>
                            <p class="text-gray-700">{{ proficiency.criteria_skill }}</p>
                        </div>
                        <div v-else>
                            <label for="criteria_skill-edit" class="text-gray-700 block font-medium">Modifier le critère
                                de
                                maitrise :</label>
                            <textarea id="criteria_skill-edit" v-model="proficiency.updatedCriteriaSkill"
                                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 min-h-20 focus:border-blue-500 p-2.5 w-full"></textarea>
                        </div>
                    </li>
                    <li class="mb-4">
                        <div v-if="!proficiency.editMode">
                            <p class="font-medium">Indicateur de maîtrise {{ index + 1 }} :</p>
                            <p class="text-gray-700">{{ proficiency.indicator }}</p>
                            <button @click="toggleProficiencyEditMode(proficiency)"
                                class="text-blue-600 hover:text-white border border-blue-600 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-2.5 py-2.5 mt-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round"
                                    stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                                </svg>
                            </button>
                            <button type="button" @click="confirmProficiencyDeletion(proficiency.id)"
                                class="text-red-600 hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-2.5 py-2.5 ml-2">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                        <div v-else>
                            <label for="indicator-edit" class="text-gray-700 block font-medium">Modifier l'indicateur de
                                maitrise :</label>
                            <textarea id="indicator-edit" v-model="proficiency.updatedIndicator"
                                class="bg-gray-200 focus:bg-gray-300 border border-gray-400 min-h-20 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full"></textarea>
                        </div>
                    </li>

                    <div v-if="proficiency.editMode">
                        <form @submit.prevent="updateProficiency(proficiency)">
                            <button type="submit"
                                class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 ">Enregistrer</button>
                            <button type="button"
                                class="focus:outline-none text-gray-700 bg-gray-300 shover:text-black border border-gray-700 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-1.5 ml-2"
                                @click="cancelProficiencyEditMode(proficiency)">Annuler</button>
                        </form>
                    </div>
                </ul>
            </div>
        </div>






        <DialogModal :show="confirmingProficiencyDeletion" @close="closeProficiencyModal">
            <template #title> Supprimer le critère de maitrise et son indicateur </template>
            <template #content>
                Êtes-vous sûr de vouloir supprimer ce critère de maitrise ainsi que son indicateur ? Cette action est
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
                Êtes-vous sûr de vouloir supprimer cet AA ? Cette action est irréversible.
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
                Êtes-vous sûr de vouloir supprimer ce critère ? Cette action est irréversible.
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
    </AppLayout>
</template>
