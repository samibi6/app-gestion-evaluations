<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps(['section']);

const form = usePrecognitionForm("patch", route("sections.update", props.section.id), {
    name: props.section.name,
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

</script>
<template>
    <AppLayout title="Sections">
        <div class="max-w-4xl mx-auto mt-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <form @submit.prevent="submit" class="max-w-lg mx-auto">
                    <h2 class="text-2xl text-center mb-4">Modifier la section</h2>

                    <div class="mb-5">
                        <label for="name" class="text-gray-700 mb-2 block font-medium">Nom de la section</label>
                        <input id="name" v-model="form.name" @change="form.validate('name')"
                            class="bg-gray-200 focus:bg-gray-300 border border-gray-400 rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 w-full" />
                        <div v-if="form.invalid('name')" class="text-sm text-red-600">
                            {{ form.errors.name }}
                        </div>
                    </div>

                    <div class="flex justify-center gap-2">
                        <button :disabled="form.processing"
                            class="focus:outline-none text-white bg-blue-900 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            Enregistrer
                        </button>

                        <a class="focus:outline-none text-gray-700 inline-flex bg-gray-300 items-center hover:text-black border border-gray-700 hover:bg-gray-400 focus:ring-4 focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5"
                            :href="route('sections.index')">
                            Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </AppLayout>
</template>
