<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import BaseLayout from "@/Layouts/BaseLayout.vue";
import Expansion from "@/Components/BaseExpansion.vue";
import Background1 from "../../../storage/imgs/home-page/home-bg1.jpg";
import Background3 from "../../../storage/imgs/home-page/bg-ranking.jpg";
import Background2 from "../../../storage/imgs/home-page/home-bg2.jpg";
import Background4 from "../../../storage/imgs/home-page/home-bg3.jpg";
import Background1Mobile from "../../../storage/imgs/home-page/home-bg1-mobile.jpg";
import Background3Mobile from "../../../storage/imgs/home-page/bg3.jpg";
import DinoLogo from "../../../storage/imgs/home-page/dino-logo.svg";
import DinoInterrogacao from "../../../storage/imgs/home-page/dino-interrogacao-bg3.svg";
import DinoRoxo from "../../../storage/imgs/home-page/dino-roxo.svg";
import firstPlace from "../../../storage/imgs/home-page/ranking/1.svg";
import secondPlace from "../../../storage/imgs/home-page/ranking/2.svg";
import thirdPlace from "../../../storage/imgs/home-page/ranking/3.svg";
import BaseModal from "../Components/BaseModal.vue";

const { canLogin, canRegister, laravelVersion, phpVersion, rankedUsers } =
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
const windowWidth = ref(window.innerWidth);

window.addEventListener("resize", (valu) => {
  windowWidth.value = window.innerWidth;
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
      return "R$10.000";
    case 1:
      return "R$5.000";
    case 2:
      return "R$1.000";
    default:
      return "-";
  }
}
</script>

<template>
  <section
    id="section1"
    class="section1 h-screen mx-auto items-center p-2 flex flex-col"
    :style="{
      backgroundImage: `url('${
        windowWidth < 700 ? Background1Mobile : Background1
      }')`,
      backgroundSize: windowWidth < 700 ? '100vw auto' : 'auto auto',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
    }"
  >
    <!-- <base-modal v-if="$page.props.errors.banned" class="box"> -->
    <div
      v-if="$page.props.errors.banned"
      class="absolute box p-6 text-red-500 text-6xl uppercase font-bold flex items-center justify-center"
      style="background-color: rgba(0, 0, 0, 0.8)"
    >
      {{ $page.props.errors.banned }}
    </div>

    <!-- </base-modal> -->
    <div
      v-if="!!!$page?.props?.auth?.user?.id"
      class="mx-auto h-20 sm:h-24 flex justify-center bg-roxo-escuro w-[280px] md:w-[430px] rounded-2xl"
    >
      <Link
        class="flex-1 flex justify-center items-center ml-3 my-auto md:w-[200px] h-[80%] font-menu text-lg md:text-3xl text-verde-claro"
        :href="route('login')"
        >{{ __("auth.login") }}</Link
      >

      <Link
        class="flex-1 flex justify-center items-center mr-3 my-auto md:w-[200px] h-[80%] bg-verde-claro rounded-lg font-menu text-lg md:text-3xl text-roxo-escuro"
        :href="route('register')"
      >
        {{ __("auth.register") }}
      </Link>
    </div>
    <img
      :src="DinoLogo"
      alt="dinoLogo"
      class="mx-auto w-3/5 md:w-[60vh] my-3 md:my-10"
    />
    <div class="w-full mx-auto mt-auto">
      <Link
        class="mx-auto mb-4 flex justify-center items-center py-2 lg:py-0 lg:w-[280px] lg:h-[80px] bg-verde-claro rounded-lg font-menu text-3xl text-black boxShadow border-black border-4"
        :href="route('user.play')"
      >
        {{ __("homepage.play-now") }}
      </Link>
      <Link
        class="mx-auto mb-4 flex justify-center items-center py-2 lg:py-0 lg:w-[280px] lg:h-[80px] bg-verde-claro rounded-lg font-menu text-3xl text-black boxShadow border-black border-4"
        :href="route('user.deposito')"
      >
        DEPOSITAR
      </Link>
      <div class="mx-auto mb-[5vh] text-xl font-menu text-white text-center">
        {{ __("homepage.register-tip") }}
      </div>
    </div>
  </section>

  <!-- <section
    id="section3"
    class="section3 h-screen mx-auto flex flex-col items-center p-2"
    :style="{
      backgroundImage: `url('${
        windowWidth < 700 ? Background3Mobile : Background3
      }')`,
      backgroundSize: windowWidth < 700 ? 'auto 100vh' : 'auto auto',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
    }"
  >
    <div class="text-center text-black font-menu text-7xl pt-3">
      RANKING GLOBAL
    </div>
    <div class="text-center text-black font-menu text-xl pt-3">
      Ranking encerra no dia 31/12/2023 às 23:59h e o valor será creditado nas
      contas dos ganhadores como saldo!
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
        <div class="flex flex-col">
          <div
            class="grid grid-cols-5 md:gap-x-0 gap-y-1 font-menu xl:text-[20px] text-xs sm:text-sm mt-5"
          >
            <div class="">Posição</div>
            <div class="col-span-2">Usuário</div>
            <div class="text-center">Distância</div>
            <div class="mb-3 text-center">Prêmio</div>
          </div>
          <div
            class="grid grid-cols-5 md:gap-x-0 font-menu xl:text-[20px] text-xs sm:text-sm mt-5"
            :class="rankedUsers.length > 7 ? 'flex-1' : 'flex-0 gap-y-3'"
          >
            <template v-for="(user, i) in rankedUsers">
              <div
                class="flex items-center col-span-3 rounded-s-full pl-2 sm:pl-4 overflow-hidden"
                :class="
                  user.email === $page.props.auth.user?.email
                    ? 'bg-purple-800 text-orange-500'
                    : 'bg-white'
                "
              >
                <img
                  class="w-3 mr-1 sm:w-4 md:w-6 sm:mr-2"
                  v-if="i < 3"
                  :src="getTrophy(i)"
                />
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
                {{ user.distancia }}M
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
    </div>
  </section> -->

  <section
    id="section2"
    class="section2 h-screen mx-auto flex flex-col items-center p-4 pb-5"
    :style="{
      backgroundImage: `url('${Background2}')`,
      backgroundSize: windowWidth < 700 ? 'auto 100vh' : 'auto auto',
      backgroundPosition: 'center',
      backgroundRepeat: 'no-repeat',
    }"
  >
    <img :src="DinoRoxo" alt="dinoLogo" class="h-[20%] z-10 top-[2%]" />
    <div
      class="mt-5 lg:-mt-5 mb-auto mr-2 flex flex-col items-center md:w-[60%] bg-white rounded-3xl border-black border-[10px] boxShadow pb-5"
    >
      <span
        class="text-center text-6xl md:text-[130px] font-menu text-gray-800 m-0 p-0"
        >Dino Cash</span
      >
      <span
        class="text-[1.8vh] lg:text-[2.2vh] max-h-[60%] font-menu text-gray-800 text-center m-0 p-2"
        >Bem-vindo ao Dinocash, sua principal opção de entretenimento e
        lucratividade em jogos online! Como uma provedora totalmente legalizada
        em Malta, oferecemos uma experiência única e inovadora no mercado.
        Explore um universo de diversão e oportunidades de lucro como nunca
        antes, desafiando seus reflexos em nossos emocionantes jogos. Junte-se a
        nós para uma jornada envolvente, onde o entretenimento se encontra com a
        chance de ganhar grandes recompensas. Venha fazer parte dessa
        experiência exclusiva no Dinocash, onde a diversão e os ganhos estão ao
        seu alcance.</span
      >
    </div>
    <Link
      class="mx-auto mt-4 p-3 flex justify-center items-center md:w-[360px] md:h-[100px] bg-verde-claro rounded-lg font-menu text-2xl md:text-[30px] text-black boxShadow border-black border-4"
      :href="route('user.play')"
    >
      JOGAR AGORA
    </Link>
    <div
      class="h-[50px] mt-6 mb-[200px] bg-roxo-escuro flex items-center rounded-2xl p-[30px]"
    >
      <span class="mx-auto md:text-2xl font-menu text-white text-center">
        {{ __("homepage.free-spin-before-register") }}
      </span>
    </div>
  </section>

  <section
    id="section4"
    class="section4 h-screen flex flex-col items-center mx-auto"
  >
    <div
      class="w-full h-full"
      :style="{
        backgroundImage: `url('${Background4}')`,
        backgroundSize: windowWidth < 700 ? 'auto 100vh' : 'auto auto',
        backgroundPosition: 'center',
        backgroundRepeat: 'no-repeat',
      }"
    >
      <div class="flex items-center justify-center h-full">
        <img
          class="h-[85%] mt-10 mr-10 hidden lg:block"
          :src="DinoInterrogacao"
          alt=""
        />
        <div>
          <div class="font-menu text-black text-9xl text-center">FAQ</div>
          <div class="flex flex-col gap-y-4 mt-5 mx-3">
            <Expansion
              title="COMO POSSO JOGAR O DINOCASH?"
              content="Você precisa fazer um depósito inicial na plataforma para começar a jogar e faturar com o dinocash."
            />
            <Expansion
              title="COMO POSSO SACAR?"
              content="O saque no dinocash é instantâneo. Utilizamos a sua chave PIX no CPF cadastrado na criação da conta para enviar o pagamento, é na hora e no PIX. 7 dias por semana e 24 horas por dia."
            />
            <Expansion
              title="É TIPO FOGUETINHO E TIGRINHO?"
              content="Não! O Dinocash é totalmente um jogo de habilidade para voce ganhar sem contar com a sorte! Vem que com o dinocash você lucra!"
            />
          </div>
        </div>
      </div>
    </div>
  </section>

  <div>
    <img
      :src="DinoLogo"
      alt="dinoLogo"
      class="h-[20%] w-80 mx-auto z-10 mt-10 px-2"
    />
    <div class="uppercase text-center font-menu">
      <div class="text-white">
        Todos os direitos reservados a dinocash - 2023
      </div>
      <div class="text-[#D1F3B4]">
        <a :href="route('terms')"
          >Leia os termos de uso e politica de privacidade</a
        >
      </div>
      <div class="text-white mt-3">contato@dinocash.io</div>
    </div>
  </div>
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
