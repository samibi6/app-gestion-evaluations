<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['sections', 'message']);

const form = usePrecognitionForm("post", route("sections.store"), {
    name: '',
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

let confirmSectionDelete = ref(false);
let sectionIdToDelete = ref(null);
let sectionToDelete = ref(null);

function confirmingSectionDeletion(section) {
    sectionIdToDelete.value = section.id;
    sectionToDelete.value = section
    confirmSectionDelete.value = true;
};

const closeModal = () => {
    confirmSectionDelete.value = false;
    deleteForm.reset();
};

const deleteForm = useForm("delete", {});

const deleteSection = () => {
    deleteForm.delete(route('sections.delete', sectionIdToDelete.value), {
        preserveScroll: true,
        onSuccess: () => {
            closeModal();
            confirmSectionDelete.value = false;
        },
        onFinish: () => deleteForm.reset(),
    });
};


</script>
<template>
    <AppLayout title="Sections">

        <h1 class="text-center font-bold text-2xl mt-12">
            Ajouter et modifier des sections
        </h1>
        <p class="text-center text-gray-700 text-lg mt-4">Ici vous pouvez consulter, ajouter, modifier et supprimer des
            sections</p>

        <div class="max-w-4xl mx-auto mt-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form @submit.prevent="submit" class="max-w-lg mx-auto">
                    <h2 class="text-2xl text-center mb-4">Créer une section</h2>

                    <div class="mb-5">
                        <label for="name" class="text-gray-700 mb-2 block font-medium">Nom de la section</label>
                        <input id="name" v-model="form.name" @change="form.validate('name')"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                        <div v-if="form.invalid('name')" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="flex justify-center mb-16">
                        <button :disabled="form.processing"
                            class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300  font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 block">
                            Ajouter la section
                        </button>
                    </div>
                </form>
                <div v-if="message">{{ message }}</div>
                <ul>
                    <li class="bg-zinc-200 my-3 flex justify-center items-center pl-4" v-for="section in sections">
                        {{ section.id + ". " + section.name }}

                        <a class=" ml-auto cursor-pointer font-bold text-blue-500 hover:bg-blue-500 hover:text-white transition h-full inline-block px-4 py-2"
                            :href="route('sections.edit', section)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-linecap="round"
                                stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7" />
                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z" />
                            </svg>
                        </a>
                        <a class="cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2"
                            @click="confirmingSectionDeletion(section)">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <DialogModal :show="confirmSectionDelete" @close="closeModal">
            <template #title>
                Suppression de la Section
            </template>

            <template #content>
                Êtes-vous sûr de vouloir supprimer la section {{ sectionToDelete.name }} ?
            </template>

            <template #footer>
                <SecondaryButton @click="closeModal">
                    Annuler
                </SecondaryButton>

                <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="deleteForm.processing"
                    @click="deleteSection">
                    Supprimer la section
                </DangerButton>
            </template>
        </DialogModal>
    </AppLayout>
</template>
