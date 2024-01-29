<script setup>
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
    email: user.email,
    wallet: user.wallet,
    isAffiliate: user.isAffiliate,
    invitation_link: user.invitation_link,
});
</script>

<template>
    <form
        @submit.prevent="form.patch(route('user.update'))"
        class="mt-6 space-y-4 w-full"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3 w-full">
            <div>
                <div class="ml-3 text-sm">Nome</div>
                <input
                    id="name"
                    type="text"
                    class="mt-1 px-4 block user-input w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
            </div>

            <div>
                <div class="ml-3 text-sm">Email</div>
                <input
                    id="email"
                    type="text"
                    class="mt-1 block user-input w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="email"
                />
            </div>

            <template v-if="!user.isAffiliate">
                <div>
                    <div class="ml-3 text-sm">Saldo da Carteira</div>

                    <input
                        id="wallet"
                        type="number"
                        class="mt-1 block w-full user-input"
                        v-model="form.wallet"
                        required
                        autofocus
                    />
                </div>
                <div>
                    <div class="ml-3 text-sm">
                        username para Link de Cadastro
                    </div>

                    <input
                        id="invitation_link"
                        type="text"
                        class="mt-1 block w-full user-input"
                        v-model="form.invitation_link"
                        required
                        autofocus
                    />
                </div>
            </template>
        </div>

        <div v-if="mustVerifyEmail && user.email_verified_at === null">
            <p class="text-sm mt-2">
                Seu endereço de e-mail não foi verificado.
                <Link
                    :href="route('verification.send')"
                    method="post"
                    as="button"
                    class="underline text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Clique aqui para reenviar o e-mail de verificação.
                </Link>
            </p>

            <div
                v-show="status === 'verification-link-sent'"
                class="mt-2 font-medium text-sm text-green-600"
            >
                Um novo link de verificação foi enviado para seu endereço de
                e-mail.
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button class="user-button" :disabled="form.processing">
                Salvar
            </button>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">
                    Salvou !
                </p>
            </Transition>
        </div>
    </form>
</template>
