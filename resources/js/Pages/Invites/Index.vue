<script setup>
import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useForm as usePrecognitionForm } from "laravel-precognition-vue-inertia";
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import AppLayout from '@/Layouts/AppLayout.vue';


const props = defineProps(['invites', 'message']);

const form = usePrecognitionForm("post", route("invites.store"), {
    email: '',
});

form.setValidationTimeout(300);

const submit = () => form.submit({
    preserveScroll: true,
    onSuccess: () => form.reset(),
});

</script>
<template>
    <AppLayout>
        <div class="bg-white w-fit p-5 mx-auto">
            <div v-if="message">{{ message }}</div>
            <form @submit.prevent="submit">
                <label class="font-bold mr-2" for="email">Adresse mail invitation:</label>
                <input type="email" name="email" id="email" v-model="form.email" @change="form.validate('email')" />
                <div v-if="form.invalid('email')">
                    {{ form.errors.email }}
                </div>
                <button :disabled="form.processing" type="submit"
                    class="font-bold bg-white p-3 hover:bg-green-500 shadow-lg rounded-full ml-3">
                    Créer Invitation
                </button>
            </form>
            <hr class="my-5">
            <h2 class="font-bold text-lg mb-2">Liste des invitations:</h2>
            <ul class="flex justify-between border-2 border-zinc-300 mb-2" v-for="invite in invites">
                <li><span class="font-bold mr-1">Email:</span>{{ invite.email }}</li>
                <li><span class="font-bold mr-1 ml-2">Token:</span>{{ invite.token }}</li>
            </ul>
        </div>
    </AppLayout>
</template>
