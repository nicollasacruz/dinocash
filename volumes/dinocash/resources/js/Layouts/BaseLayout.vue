<script setup>
import ApplicationLogo from "@/Components/ApplicationLogo.vue";
import { Link } from "@inertiajs/vue3";
import DinoLogo from "../../../storage/imgs/home-page/dino-logo.svg";
import Background1 from "../../../storage/imgs/home-page/home-bg1.jpg";
import BackgroundMobile from "../../../storage/imgs/home-page/home-bg1-mobile.jpg";
import BannerTop from "@/Pages/User/BannerTop.vue";

function isMobile() {
  if (screen.width <= 760) {
    return true;
  }
  return false;
}

function isIpad() {
  if (screen.width > 760 && screen.width < 1000) {
    return true;
  }
  return false;
}

function isPc() {
  if (!isIpad() && !isMobile()) {
    return true;
  }
  return false;
}
const props = defineProps({
  homepage: {
    type: Boolean,
    default: false,
  },
});
</script>

<template>
  <BannerTop />
  <div
    :style="{
      backgroundImage: `url('${
        windowWidth < 700 ? Background1Mobile : Background1
      }')`,
      backgroundSize: windowWidth < 700 ? 'auto 100vh' : 'auto auto',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
    }"
    class="w-screen h-screen bg-[#6c567c] p-3 flex flex-col"
    :class="homepage ? '' : 'overflow-hidden'"
  >
    <div id="menu">
      <nav class="h-16 sm:h-20 w-auto flex justify-center content-center">
        <div
          v-if="!!!$page?.props?.auth?.user?.id"
          class="flex justify-center bg-roxo-escuro w-[330px] md:w-[430px] rounded-2xl"
        >
          <Link
            class="flex-1 flex justify-center items-center ml-3 my-auto md:w-[200px] h-[80%] font-menu text-xl md:text-3xl text-verde-claro"
            :href="route('login')"
            >{{ __("auth.login") }}</Link
          >

          <Link
            class="flex-1 flex justify-center items-center mr-3 my-auto md:w-[200px] h-[80%] bg-verde-claro rounded-lg font-menu text-xl md:text-3xl text-roxo-escuro"
            :href="route('register')"
          >
            {{ __("auth.register") }}
          </Link>
        </div>

        <div
          v-if="$page?.props?.auth?.user"
          class="flex divide-x divide-dashed"
        >
          <div v-if="isPc()" class="flex justify-center content-center">
            <Link
              class="flex justify-center items-center my-auto w-[200px] h-[80%] font-menu text-3xl text-verde-claro"
              :href="route('login')"
            >
              <img :src="DinoLogo" alt="dinoLogo" class="h-[90%]" />
            </Link>
          </div>
          <div class="flex content-center justify-center items-center p-4">
            <Link
              class="flex justify-center items-center mx-3 bg-verde-claro rounded-lg font-menu text-3xl text-roxo-escuro p-2"
              :href="route('login')"
            >
              {{ __("nav.play") }}
            </Link>
          </div>
          <div class="flex content-center justify-center items-center p-4">
            <Link
              class="flex justify-center items-center mx-3 rounded-lg font-menu text-3xl text-verde-claro"
              :href="route('login')"
            >
              {{ __("nav.depositar") }}
            </Link>
          </div>
          <div class="flex content-center justify-center items-center p-4">
            <Link
              class="flex justify-center items-center mx-3 rounded-lg font-menu text-3xl text-verde-claro"
              :href="route('login')"
            >
              {{ __("nav.panel") }}
            </Link>
          </div>
          <div class="flex content-center justify-center items-center p-4">
            <Link
              class="flex justify-center items-center mx-3 rounded-lg font-menu text-2xl text-verde-claro"
              :href="route('logout')"
              method="post"
            >
              {{ __("nav.logout") }}
            </Link>
          </div>
        </div>
      </nav>
    </div>

    <img
      :src="DinoLogo"
      alt="dinoLogo"
      v-if="!homepage"
      class="h-[16%] my-3 md:my-4 mx-auto"
    />

    <slot class="" />
  </div>
</template>
