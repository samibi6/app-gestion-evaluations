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
    <AppLayout>
        <div class="bg-white w-fit mx-auto p-5">
            <form @submit.prevent="submit">
                <label class="mr-2" for="name">Nom de la section: </label>
                <input id="name" v-model="form.name" @change="form.validate('name')" />
                <div v-if="form.invalid('name')">
                    {{ form.errors.name }}
                </div>
                <button :disabled="form.processing" type="submit"
                    class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full ml-3">
                    Ajouter la section
                </button>
            </form>
            <div v-if="message">{{ message }}</div>
            <ul>
                <li class="bg-zinc-200 my-3 flex justify-center items-center pl-4" v-for="section in sections">{{
                    section.id + ". " + section.name
                }}<a class="ml-auto cursor-pointer font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2"
                        @click="confirmingSectionDeletion(section)">Delete</a><a
                        class="cursor-pointer font-bold text-blue-500 hover:bg-blue-500 hover:text-white transition h-full inline-block px-4 py-2"
                        :href="route('sections.edit', section)">Modifier</a></li>
            </ul>
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
        </div>
    </AppLayout>
</template>
