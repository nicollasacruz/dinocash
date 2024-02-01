<script setup>
import BaseLayout from "@/Layouts/BaseLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Background1 from "../../../../storage/imgs/home-page/home-bg1.jpg";
import DinoLogo from "../../../../storage/imgs/home-page/dino-logo.svg";

const form = useForm({
    name: "",
    email: "",
    contact: "",
    password: "",
    password_confirmation: "",
    document: "",
});

const submit = () => {
    if (isPhoneNumberValid(form.contact)) {
        form.post(route("register"), {
            onFinish: () => form.reset("password", "password_confirmation"),
        });
    }
};

const isPhoneNumberValid = (phoneNumber) => {
    const regex = /^\(\d{2}\)\d{5}-\d{4}$/;
    return regex.test(phoneNumber);
};
const isDocumentNumberValid = (document) => {
    const regex = /^[0-9]{3}\.?[0-9]{3}\.?[0-9]{3}\-?[0-9]{2}$/;
    return regex.test(document);
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
                <form @submit.prevent="submit" class="mx-auto lg:w-[40%]">
                    <div>
                        <TextInput
                            id="name"
                            type="text"
                            class="mt-1 block w-full login-input border-none placeholder:text-gray-500 placeholder:font-menu placeholder:text-2xl"
                            v-model="form.name"
                            required
                            autofocus
                            autocomplete="name"
                            v-bind:placeholder="__('auth.name')"
                        />

                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>

                    <div class="mt-4">
                        <TextInput
                            id="email"
                            type="email"
                            class="mt-1 block w-full login-input border-none placeholder:text-gray-500 placeholder:font-menu placeholder:text-2xl"
                            v-model="form.email"
                            required
                            autocomplete="username"
                            v-bind:placeholder="__('auth.email')"
                        />

                        <InputError class="mt-2" :message="form.errors.email" />
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
                            id="document"
                            type="text"
                            class="mt-1 block w-full login-input border-none"
                            v-model="form.document"
                            required
                            autocomplete="document"
                            v-mask="'###.###.###-##'"
                            v-bind:placeholder="__('auth.document')"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.contact"
                        />

                        <div
                            v-if="
                                form.document &&
                                !isDocumentNumberValid(form.document)
                            "
                            class="text-red-600 mt-2"
                        >
                            O CPF deve estar no formato ###.###.###-##.
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

                    <div class="mt-4">
                        <TextInput
                            id="password_confirmation"
                            type="password"
                            class="mt-1 block w-full login-input border-none"
                            v-model="form.password_confirmation"
                            required
                            autocomplete="new-password"
                            v-bind:placeholder="__('auth.confirm-password')"
                        />

                        <InputError
                            class="mt-2"
                            :message="form.errors.password_confirmation"
                        />
                    </div>

                    <div
                        class="flex flex-col items-center justify-center mt-5 mx-auto"
                    >
                        <PrimaryButton
                            class="!bg-verde flex justify-center items-center w-full py-4 text-xl capitalize rounded-xl user-button"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            {{ __("auth.register") }}
                        </PrimaryButton>
                        <!-- <Link
                            :href="route('login')"
                            class="mx-auto font-menu underline text-xl md:text-2xl text-white hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            {{ __("auth.already-registered") }}
                        </Link> -->
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
