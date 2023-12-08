<script setup>
import { Head, Link } from "@inertiajs/vue3";
import BaseLayout from "@/Layouts/BaseLayout.vue";
import Expansion from "@/Components/BaseExpansion.vue";
import Background1 from "../../../storage/imgs/home-page/home-bg1.jpg";
import Background2 from "../../../storage/imgs/home-page/home-bg2.jpg";
import Background3 from "../../../storage/imgs/home-page/bg-ranking.png";
import Background4 from "../../../storage/imgs/home-page/home-bg3.jpg";
import DinoLogo from "../../../storage/imgs/home-page/dino-logo.svg";
import DinoInterrogacao from "../../../storage/imgs/home-page/dino-interrogacao-bg3.svg";
import DinoRoxo from "../../../storage/imgs/home-page/dino-roxo.svg";
import firstPlace from "../../../storage/imgs/home-page/ranking/1.svg";
import secondPlace from "../../../storage/imgs/home-page/ranking/2.svg";
import thirdPlace from "../../../storage/imgs/home-page/ranking/3.svg";

defineProps({
  canLogin: {
    type: Boolean,
  },
  canRegister: {
    type: Boolean,
  },
  laravelVersion: {
    type: String,
    required: true,
  },
  phpVersion: {
    type: String,
    required: true,
  },
  rankedUsers: {
    type: Array,
    required: true,
  },
});

function getTrophy(i) {
  switch (i) {
    case 0:
      return firstPlace;
    case 1:
      return secondPlace;
    case 2:
      return thirdPlace;
    default:
      return "";
  }
}
function getPremio(i) {
  switch (i) {
    case 0:
      return "R$2.000,00";
    case 1:
      return "R$1.000,00";
    case 2:
      return "R$500,00";
    default:
      return "-";
  }
}
</script>

<template>
  <Head title="Home" />
  <section
    id="section1"
    class="section1 h-screen mx-auto items-center p-2 flex flex-col"
    :style="{
      backgroundImage: `url('${Background1}')`,
      backgroundSize: 'auto auto',
    }"
  >
    <div
      v-if="!!!$page?.props?.auth?.user?.id"
      class="mx-auto h-20 sm:h-24 flex justify-center bg-roxo-escuro w-[280px] md:w-[430px] rounded-2xl"
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
    <img
      v-if="!!$page?.props?.auth?.user?.id"
      :src="DinoLogo"
      alt="dinoLogo"
      class="mx-auto w-full max-w-lg my-3 md:my-10"
    />
    <img
      v-if="!!!$page?.props?.auth?.user?.id"
      :src="DinoLogo"
      alt="dinoLogo"
      class="mx-auto w-full max-w-lg my-3 md:my-10"
    />
    <div class="w-full mx-auto mt-auto">
      <Link
        class="mx-auto mb-4 flex justify-center items-center w-[280px] h-[80px] bg-verde-claro rounded-lg font-menu text-3xl text-roxo-fundo boxShadow border-black border-4"
        :href="route('user.play')"
      >
        {{ __("homepage.play-now") }}
      </Link>
      <Link
        class="mx-auto mb-4 flex justify-center items-center w-[280px] h-[80px] bg-verde-claro rounded-lg font-menu text-3xl text-roxo-fundo boxShadow border-black border-4"
        :href="route('user.deposito')"
      >
        DEPOSITAR
      </Link>
      <div class="mx-auto mb-[10vh] text-xl font-menu text-white text-center">
        {{ __("homepage.register-tip") }}
      </div>
    </div>
  </section>

  <section
    id="section2"
    class="section2 h-screen mx-auto flex flex-col items-center p-4 pb-5"
    :style="{ backgroundImage: `url('${Background2}')` }"
  >
    <img :src="DinoRoxo" alt="dinoLogo" class="h-[20%] z-10 top-[2%]" />
    <div
      class="-mt-5 mb-auto mr-2 flex flex-col items-center md:w-[60%] bg-white rounded-3xl border-black border-[10px] boxShadow pb-5"
    >
      <span
        class="text-center text-6xl md:text-[130px] font-menu text-gray-800 m-0 p-0"
        >DINO CA$H</span
      >
      <span
        class="text-[2.2vh] max-h-[60%] font-menu text-gray-800 text-center m-0 p-0"
        >Lorem ipsum dolor sit amet consectetur adipisicing elit. Quo enim odit
        debitis ab voluptas, ullam commodi accusantium amet, aliquam doloribus
        magnam nam cum rerum. Aut excepturi qui a temporibus suscipit! Lorem
        ipsum dolor sit amet consectetur adipisicing elit.</span
      >
    </div>
    <Link
      class="mx-auto mt-4 p-3 flex justify-center items-center md:w-[360px] md:h-[100px] bg-verde-claro rounded-lg font-menu text-4xl md:text-[50px] text-roxo-fundo boxShadow border-black border-4"
      :href="route('user.play')"
    >
      {{ __("homepage.play-now") }}
    </Link>
    <div
      class="h-[50px] mt-6 bg-roxo-escuro flex items-center rounded-2xl p-[30px]"
    >
      <span class="mx-auto md:text-2xl font-menu text-white text-center">
        {{ __("homepage.free-spin-before-register") }}
      </span>
    </div>
  </section>

  <section
    id="section3"
    class="section3 h-screen mx-auto flex flex-col items-center p-2"
    :style="{ backgroundImage: `url('${Background3}')` }"
  >
    <div class="text-center text-white font-menu text-7xl pt-3">
      RANKING GLOBAL
    </div>
    <div
      class="p-1 mt-3 h-[70%] mb-auto mr-2 flex flex-col items-center md:w-[60%] bg-white rounded-3xl border-black border-[10px] boxShadow"
    >
      <div class="flex w-full h-full">
        <div class="hidden w-60 lg:flex flex-col">
          <div class="flex justify-center items-end mx-auto mt-14">
            <img class="w-7" :src="thirdPlace" />
            <img class="w-24" :src="firstPlace" />
            <img class="w-7" :src="secondPlace" />
          </div>
          <img :src="DinoRoxo" alt="dinoLogo" class="h-60 -ml-10 mt-auto" />
        </div>
        <div
          class="flex-1 grid grid-cols-5 gap-x-1 md:gap-x-0 font-menu md:text-[25px] mt-5"
        >
          <div class="">Posição</div>
          <div class="col-span-2">Usuário</div>
          <div class="text-center">Distância</div>
          <div class="mb-3 text-center">Prêmio</div>
          <template v-for="(user, i) in rankedUsers">
            <div
              class="flex items-center col-span-3 rounded-s-full pl-4"
              :class="
                user.email === $page.props.auth.user?.email
                  ? 'bg-purple-800 text-orange-500'
                  : 'bg-white'
              "
            >
              <img class="w-6 mr-2" v-if="i < 3" :src="getTrophy(i)" />
              <span class="mr-2" v-else>{{ user.posicao }}º</span>
              {{ user.email }}
            </div>
            <div
              class="flex items-center justify-center text-center"
              :class="
                user.email === $page.props.auth.user?.email
                  ? 'bg-purple-800 text-orange-500'
                  : 'bg-white'
              "
            >
              {{ user.distancia }} M
            </div>
            <div
              class="flex items-center justify-center rounded-r-full"
              :class="
                user.email === $page.props.auth.user?.email
                  ? 'bg-purple-800 text-orange-500'
                  : 'bg-white'
              "
            >
              {{ getPremio(i) }}
            </div>
          </template>
        </div>
      </div>
    </div>
  </section>

  <section
    id="section4"
    class="section4 h-screen flex flex-col items-center p-4 mx-auto"
  >
    <div
      class="content mx-auto max-w-[1920px]"
      :style="{ backgroundImage: `url('${Background4}')` }"
    >
      <div class="flex h-full">
        <img
          class="h-[85%] mt-10 mr-10 hidden lg:block"
          :src="DinoInterrogacao"
          alt=""
        />
        <div>
          <div class="font-menu text-9xl text-center">FAQ</div>
          <div class="flex flex-col gap-y-4 mt-5">
            <Expansion
              title="Como funciona o dinoca$h?"
              content="lorem 
                        ipsum dolor sit amet consectetur adipisicing elit. Quo enim odit
                        debitis ab voluptas, ullam commodi accusantium amet, aliquam
                        "
            />
            <Expansion
              title="Como funciona o dinoca$h?"
              content="lorem 
                        ipsum dolor sit amet consectetur adipisicing elit. Quo enim odit
                        debitis ab voluptas, ullam commodi accusantium amet, aliquam
                        "
            />
            <Expansion
              title="Como funciona o dinoca$h?"
              content="lorem 
                        ipsum dolor sit amet consectetur adipisicing elit. Quo enim odit
                        debitis ab voluptas, ullam commodi accusantium amet, aliquam
                        "
            />
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer></footer>
</template>

<style>
.bg-dots-darker {
  background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(0,0,0,0.07)'/%3E%3C/svg%3E");
}

@media (prefers-color-scheme: dark) {
  .dark\:bg-dots-lighter {
    background-image: url("data:image/svg+xml,%3Csvg width='30' height='30' viewBox='0 0 30 30' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1.22676 0C1.91374 0 2.45351 0.539773 2.45351 1.22676C2.45351 1.91374 1.91374 2.45351 1.22676 2.45351C0.539773 2.45351 0 1.91374 0 1.22676C0 0.539773 0.539773 0 1.22676 0Z' fill='rgba(255,255,255,0.07)'/%3E%3C/svg%3E");
  }
}

.boxShadow {
  box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.85);
  -webkit-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.85);
  -moz-box-shadow: 10px 10px 0px 0px rgba(0, 0, 0, 0.85);
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

.section2 {
  background-color: rgb(144, 188, 175);
}

.section3 {
  background-color: rgb(240, 225, 168);
}

.section4 {
  background-color: rgb(240, 225, 168);
}
</style>
