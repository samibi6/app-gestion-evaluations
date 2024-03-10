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

const props = defineProps(['course', 'users', 'sections', 'usersByCurrentCourse', 'sectionsByCurrentCourse']);
const form = usePrecognitionForm("patch", route("courses.update", { course: props.course.id, }), {
    name: props.course.name,
    code: props.course.code,
    section: props.sectionsByCurrentCourse.length > 0 ? props.sectionsByCurrentCourse[0].id : null,
    user: props.usersByCurrentCourse.length > 0 ? props.usersByCurrentCourse[0].id : null,
    lead: props.course.lead,
    is_tfe: props.course.is_tfe === 0 ? false : true,
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

const cancel = () => {
    return back();
}


</script>

<template>
    <AppLayout title="Cours">
        <div class="max-w-4xl mx-auto mt-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form @submit.prevent="submit" class="max-w-lg mx-auto">
                    <h2 class="text-2xl text-center mb-4">Modifier l'UE</h2>

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

                    <div class="flex justify-center gap-2">
                        <button :disabled="form.processing"
                            class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            Enregistrer
                        </button>


                        <a class="focus:outline-none text-gray-700 inline-flex items-center bg-gray-300 hover:text-black border border-gray-700 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5"
                            :href="route('courses.index')">
                            Annuler
                        </a>

                        <button type="button"
                            class="focus:outline-none text-red-600 inline-flex items-center hover:text-white border border-red-600 hover:bg-red-600 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 ml-12"
                            @click="confirmCourseDeletion(course.id)">
                            <svg class="w-5 h-5 mr-1 -ml-1" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Supprimer l'UE
                        </button>
                    </div>

                </form>
            </div>
        </div>


        <DialogModal :show="confirmingCourseDeletion" @close="closeModal">
            <template #title> Supprimer l'UE </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer cette UE ? Cette action est
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