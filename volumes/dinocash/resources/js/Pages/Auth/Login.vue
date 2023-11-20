<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import BaseLayout from '@/Layouts/BaseLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Background1 from '../../../../storage/imgs/home-page/home-bg1.jpg';
import DinoLogo from '../../../../storage/imgs/home-page/dino-logo.svg';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <BaseLayout>

        <Head title="Log in" />
        <section id="section1" class="section1 w-screen h-screen mx-auto items-center">
            <div class="content mx-auto max-w-[1920px] flex flex-col justify-end"
                :style="{ backgroundImage: `url('${Background1}')` }">
                <img :src="DinoLogo" alt="dinoLogo" class="h-[22%] mb-[10vh]">
                <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class=" mx-auto w-[40%]">
                    <div>
                        <!-- <InputLabel for="email" value="Email" /> -->

                        <TextInput id="email" type="email"
                            class="mt-1 block w-full placeholder:text-gray-500 placeholder:font-menu placeholder:text-2xl"
                            v-model="form.email" required autofocus autocomplete="username" v-bind:placeholder="__('messages.email')" />

                        <InputError class="mt-2" :message="form.errors.email" />
                    </div>

                    <div class="mt-4">
                        <!-- <InputLabel for="password" value="Password" /> -->

                        <TextInput id="password" type="password"
                            class="mt-1 block w-full placeholder:text-gray-500 placeholder:font-menu placeholder:text-2xl"
                            v-model="form.password" required autocomplete="current-password" v-bind:placeholder="__('messages.password')" />

                        <InputError class="mt-2" :message="form.errors.password" />
                    </div>

                    <div class="block mt-4">
                        <label class="flex items-center">
                            <Checkbox name="remember" v-model:checked="form.remember" />
                            <span class="ms-2 text-xl text-gray-900 font-menu">
                                {{ __('messages.remember') }}
                            </span>
                        </label>
                    </div>

                    <div class="flex flex-col items-center justify-center mt-4 mx-auto">

                        <PrimaryButton
                            class="ms-4 mb-4 flex justify-center items-center w-[300px] h-[80px] bg-verde-claro rounded-lg font-menu text-2xl text-black boxShadow border-black border-4"
                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            {{ __('messages.login') }}
                        </PrimaryButton>
                        <Link v-if="canResetPassword" :href="route('password.request')"
                            class="mx-auto mb-[20%] font-menu underline text-2xl text-white hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        {{ __('messages.forgot_password') }}
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

.section1 {
    background-color: rgb(108, 86, 124);
}
</style>