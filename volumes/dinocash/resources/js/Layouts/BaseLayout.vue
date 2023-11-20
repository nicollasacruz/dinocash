<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import DinoLogo from '../../../storage/imgs/home-page/dino-logo.svg';
</script>

<template>
  <div class="w-screen">

    <div id="menu" class="fixed z-50 left-1/2 top-10 transform -translate-x-1/2">
      <!-- <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
                </Link> -->

      <nav class="h-24 bg-roxo-escuro rounded-2xl flex flex-row justify-center content-center">
        <div v-if="!!!$page?.props?.auth?.user?.id" class="flex">
          <div class="flex justify-center content-center">
            <Link
              class="flex justify-center items-center ml-3 my-auto w-[200px] h-[80%] font-menu text-3xl text-verde-claro"
              :href="route('login')">{{ __('auth.login') }}</Link>
          </div>
          <div class="flex content-center justify-center items-center">
            <Link
              class=" flex justify-center items-center mr-3 my-auto w-[200px] h-[80%] bg-verde-claro rounded-lg font-menu text-3xl text-roxo-escuro"
              :href="route('register')">
            {{ __('auth.register') }}
            </Link>
          </div>
        </div>

        <div v-if="$page?.props?.auth?.user" class="flex divide-x divide-dashed ">
          <div class="flex justify-center content-center">
            <Link
              class="flex justify-center items-center my-auto w-[200px] h-[80%] font-menu text-3xl text-verde-claro"
              :href="route('login')">
            <img :src="DinoLogo" alt="dinoLogo" class="h-[90%]">
            </Link>
          </div>
          <div class="flex content-center justify-center items-center p-4">
            <Link
              class="flex justify-center items-center mx-3 bg-verde-claro rounded-lg font-menu text-3xl text-roxo-escuro p-2"
              :href="route('login')">
            {{ __('nav.play') }}
            </Link>
          </div>
          <div class="flex content-center justify-center items-center p-4">
            <Link
              class="flex justify-center items-center mx-3 rounded-lg font-menu text-3xl text-verde-claro"
              :href="route('login')">
            {{ __('nav.depositar') }}
            </Link>
          </div>
          <div class="flex content-center justify-center items-center p-4">
            <Link
              class="flex justify-center items-center mx-3 rounded-lg font-menu text-3xl text-verde-claro"
              :href="route('login')">
            {{ __('nav.panel') }}
            </Link>
          </div>
          <div class="flex content-center justify-center items-center p-4">
            <Link
              class="flex justify-center items-center mx-3 rounded-lg font-menu text-2xl text-verde-claro"
              :href="route('logout')" method="post">
            {{ __('nav.logout') }}
            </Link>
          </div>

        </div>
      </nav>
    </div>

    <div class="">
      <slot />
    </div>
  </div>
</template>

