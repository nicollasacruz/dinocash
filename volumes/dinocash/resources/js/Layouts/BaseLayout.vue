<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
  <div class="w-screen">

    <div id="menu" class="fixed z-50 left-1/2 top-10 transform -translate-x-1/2">
      <!-- <Link href="/">
              <ApplicationLogo class="w-20 h-20 fill-current text-gray-500" />
              </Link> -->

      <nav class="h-24 bg-roxo-escuro rounded-lg flex flex-row justify-center content-center">
        <div :v-if="$page.props.auth.user" class="flex">
          <div class="flex justify-center content-center">
            <Link
              class="flex justify-center items-center ml-3 my-auto w-[200px] h-[80%] font-menu text-3xl text-verde-claro"
              :href="route('login')">LOGIN</Link>
          </div>
          <div class="flex content-center justify-center items-center">
            <Link
              class=" flex justify-center items-center mr-3 my-auto w-[200px] h-[80%] bg-verde-claro rounded-lg font-menu text-3xl text-roxo-escuro"
              :href="route('register')">
            CADASTRAR
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

