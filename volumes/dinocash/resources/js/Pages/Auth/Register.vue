<script setup>
import BaseLayout from "@/Layouts/BaseLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Background1 from "../../../../storage/imgs/home-page/home-bg1.jpg";
import DinoLogo from "../../../../storage/imgs/home-page/Logotipo nova.png";

const form = useForm({
    username: "",
    contact: "",
    password: ""
});
window.fbq('track', 'Criar conta')
const submit = () => {
    if (isPhoneNumberValid(form.contact)) {
        form.post(route("register"), {
            onFinish: () => form.reset("password"),
            onSuccess: () => {
                window.fbq('track', 'Conta criada')
            }
        });
    }
};

const isPhoneNumberValid = (phoneNumber) => {
    const regex = /^\(\d{2}\)\d{5}-\d{4}$/;
    return regex.test(phoneNumber);
};
</script>

<template>
    <BaseLayout>
        <Head title="Register" />
        <section class="">
            <div
                class="content mx-auto max-w-[1920px] flex flex-col justify-center my-auto"
            >
                <div
                    v-if="status"
                    class="mb-4 font-medium text-sm text-green-600"
                >
                    {{ status }}
                </div>
                <form @submit.prevent="submit" class="mx-auto w-10/12 md:w-8/12 lg:w-1/5">
                    <div class="mt-4">
                        <TextInput
                            id="username"
                            type="username"
                            class="mt-1 block w-full login-input border-none placeholder:text-gray-500 placeholder:font-menu placeholder:text-2xl"
                            v-model="form.username"
                            required
                            autocomplete="username"
                            v-bind:placeholder="__('auth.username')"
                        />

                        <InputError class="mt-2" :message="form.errors.username" />
                    </div>

                    <div class="mt-4">
                        <TextInput
                            id="contact"
                            type="text"
                            class="mt-1 block w-full login-input border-none"
                            v-model="form.contact"
                            required
                            autocomplete="contact"
                            v-mask="'(##)#####-####'"
                            v-bind:placeholder="__('auth.contact')"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.contact"
                        />

                        <div
                            v-if="
                                form.contact &&
                                !isPhoneNumberValid(form.contact)
                            "
                            class="text-red-600 mt-2"
                        >
                            O número de telefone deve estar no formato
                            (xx)xxxxx-xxxx.
                        </div>
                    </div>

                    <div class="mt-4">
                        <TextInput
                            id="password"
                            type="password"
                            class="mt-1 block w-full login-input border-none"
                            v-model="form.password"
                            required
                            autocomplete="new-password"
                            v-bind:placeholder="__('auth.password')"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.password"
                        />
                    </div>

                    <div
                        class="flex flex-col items-center justify-center mt-4 mx-auto"
                    >
                        <PrimaryButton
                            class="!bg-verde flex justify-center items-center w-full py-4 text-xl capitalize rounded-xl user-button"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ __("auth.register") }}
                        </PrimaryButton>
                        <Link
                            :href="route('login')"
                            class="mx-auto font-menu underline text-xl md:text-2xl text-white hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            {{ __("auth.already-registered") }}
                        </Link>
                    </div>
                </form>
            </div>
        </section>
    </BaseLayout>
</template>

<style>
.boxShadow {
    box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.75);
    -webkit-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.75);
    -moz-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.75);
}

.content {
    max-width: 1920px;
    height: 100%;
    background-size: auto 100vh;
    background-repeat: no-repeat;
    background-position: center;
}
</style>
