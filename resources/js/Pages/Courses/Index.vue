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
import { ref } from "vue";

const props = defineProps(['courses', 'users', 'sections', 'sectionsByCourses', 'usersByCourses']);



const form = usePrecognitionForm("post", route("courses.store"), {
    name: '',
    code: '',
    section: '',
    user: '',
    lead: '',
    is_tfe: false,

});

form.setValidationTimeout(300);




const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

const confirmingCourseDeletion = ref(false);
const courseIdToDelete = ref(null);
const formDeleteCourse = useForm("delete", {});

const confirmCourseDeletion = (id) => {
    courseIdToDelete.value = id;
    confirmingCourseDeletion.value = true;
};

var deleteCourse = () => {
    formDeleteCourse.delete(route("courses.delete", courseIdToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            confirmingCourseDeletion.value = false;
        },
    });
};

var closeModal = () => {
    confirmingCourseDeletion.value = false;
};
</script>

<template>
    <AppLayout title="Cours">

        <template #header>
            <h1 class="text-center font-bold text-2xl mt-12">
                Ajouter et modifier des cours
            </h1>
            <p class="text-center text-gray-700 text-lg mt-4">Ici vous pouvez consulter, ajouter, modifier et supprimer des
                unités
                d'enseignement</p>
        </template>
        <div class="max-w-4xl mx-auto mt-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form @submit.prevent="submit" class="max-w-lg mx-auto">
                    <h2 class="text-2xl text-center mb-4">Créer un cours</h2>

                    <div class="mb-5">
                        <label for="name" class="text-gray-700 mb-2 block font-medium">Nom de l'UE</label>
                        <input id="name" v-model="form.name" @change="form.validate('name')"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                        <div v-if="form.invalid('name')" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="mb-5">
                        <label for="code" class="text-gray-700 mb-2 block font-medium">Code de l'UE</label>
                        <input id="code" v-model="form.code" @change="form.validate('code')"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5 w-full" />
                        <div v-if="form.invalid('code')" class="text-sm text-red-600">
                            {{ form.errors.code }}
                        </div>
                    </div>

                    <div class="mb-5">
                        <label for="lead" class="text-gray-700 mb-2 block font-medium">Chapeau de l'UE</label>
                        <textarea id="lead" v-model="form.lead" @change="form.validate('lead')"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5 w-full min-h-36"></textarea>
                        <div v-if="form.invalid('lead')" class="text-sm text-red-600">
                            {{ form.errors.lead }}
                        </div>
                    </div>

                    <div class="mb-6">

                        <input class="mr-2 cursor-pointer" type="checkbox" name="is_tfe" id="is_tfe"
                            v-model="form.is_tfe" />
                        <label class="cursor-pointer" for="is_tfe">Épreuve intégrée</label>

                    </div>

                    <div class="mb-5">
                        <label for="section" class="text-gray-700 mb-2 block font-medium">Section de l'UE</label>
                        <select id="section" v-model="form.section" @change="form.validate('section')"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                            <option v-for="section in sections" :key="section.id" :value="section.id">{{ section.name }}
                            </option>
                        </select>
                        <div v-if="form.invalid('section')" class="text-sm text-red-600">
                            {{ form.errors.section }}
                        </div>
                    </div>

                    <div class="mb-5">
                        <label for="user" class="text-gray-700 mb-2 block font-medium">Chargé de cours</label>
                        <select id="user" v-model="form.user" @change="form.validate('user')"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full">
                            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                        <div v-if="form.invalid('user')" class="text-sm text-red-600">
                            {{ form.errors.user }}
                        </div>
                    </div>

                    <div class="flex justify-center">
                        <button :disabled="form.processing"
                            class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                            Ajouter le cours
                        </button>
                    </div>
                </form>
            </div>
        </div>


        <h2 class="text-2xl text-center mt-16 mb-4">Liste des cours</h2>

        <div class="md:mx-8 lg:mx-32 xl:mx-64 grid sm:grid-cols-1 md:grid-cols-1 lg:grid-cols-3 gap-4 mt-4 mb-12">
            <div v-for="course in courses" :key="course.id"
                class="bg-gray-300 rounded-lg border border-gray-400 shadow-xl p-4">
                <h3 class="text-lg font-semibold">{{ course.name }}</h3>
                <p class="text-gray-500 mb-2">{{ course.code }}</p>
                <p v-if="course.is_tfe" class="text-red-500 font-bold">Épreuve intégrée</p>
                <p>Sections du cours: <span v-for="section in sectionsByCourses[course.id]" :key="section.id">{{
                    section.name }}</span></p>
                <p>Chargé de cours : <span v-for="user in usersByCourses[course.id]" :key="user.id">{{ user.name
                }}</span></p>
                <div class="flex justify-between mt-4">
                    <a class="text-blue-600 inline-flex items-center hover:text-white border border-blue-600 hover:bg-blue-600 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-2.5 py-2.5"
                        :href="route('courses.edit', { course })"><svg class="w-5 h-5" fill="none" stroke="currentColor"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                        </svg></a>
                    <button type="button" @click="confirmCourseDeletion(course.id)"
                        class="text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-2.5 py-2.5">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>



        <DialogModal :show="confirmingCourseDeletion" @close="closeModal">
            <template #title> Supprimer le cours </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer ce cours ? Cette action est
                irréversible.
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal"> Annuler </SecondaryButton>

                <DangerButton class="ms-3" :class="{ 'opacity-25': confirmingCourseDeletion.processing }"
                    :disabled="confirmingCourseDeletion.processing" @click="deleteCourse">
                    Supprimer
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>

