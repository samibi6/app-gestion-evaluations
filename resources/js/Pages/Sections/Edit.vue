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
    <div>
        <form @submit.prevent="submit">
            <label for="name">Nom du cours</label>
            <input id="name" v-model="form.name" @change="form.validate('name')" />
            <div v-if="form.invalid('name')">
                {{ form.errors.name }}
            </div>
            <button :disabled="form.processing" type="submit"
                class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full">
                Enregistrer modifications
            </button>
            <a class="font-bold text-red-500 hover:bg-red-500 hover:text-white transition h-full inline-block px-4 py-2"
                :href="route('sections.index')">Annuler</a>
        </form>
    </div>
</template>
