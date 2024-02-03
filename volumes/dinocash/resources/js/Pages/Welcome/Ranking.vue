<template>
    <section
        :style="{
            backgroundImage: `url('${
                windowWidth < 700 ? bgMobile : Background
            }')`,
            backgroundSize: 'cover',
            backgroundPosition: windowWidth < 700 ? 'bottom' : 'center',
            backgroundRepeat: 'no-repeat',
        }"
        class="h-screen px-3 py-8 lg:px-14 relative"
    >
        <div
            class="text-5xl md:text-6xl font-menu mb-1 xl:mb-6 ml-4 text-verde font-bold"
        >
            Ranking
            <span class="hidden md:inline"> Global </span>
        </div>
        <div class="grid grid-cols-3 h-[80%] font-bold">
            <div class="hidden xl:block col-span-1  ">
                <img
                    :src="DinoTrofeu"
                    alt="dinoLogo"
                    class="max-w-xl mt-auto absolute bottom-0"
                />
            </div>
            <div
                class="col-span-3 xl:col-span-2 h-full flex-2 p-1 px-3 mt-3 mb-auto mr-2 flex flex-col items-center bg-[#2A1236] rounded-3xl border-roxo border-4 shadow-xl text-white"
            >
                <div class="flex w-full h-full">
                    <div class="w-32 hidden xl:block"></div>

                    <div class="flex flex-col w-full">
                        <div
                            class="grid grid-cols-4 md:grid-cols-3 md:gap-x-0 gap-y-1 font-menu md:text-xl mt-5 text-verde uppercase text-xs sm:text-md"
                        >
                            <!-- <div class="">Posição</div> -->
                            <div class="col-span-2 md:col-span-1 text-center">
                                Jogador
                            </div>
                            <div class="text-center">Metragem</div>
                            <div class="mb-3 text-center">Prêmio</div>
                        </div>
                        <div
                            class="grid grid-cols-4 md:grid-cols-3 md:gap-x-0 font-menu md:mt-2 text-xs sm:text-md lg:text-lg"
                            :class="
                                rankedUsers.length > 7
                                    ? 'flex-1'
                                    : 'flex-0 gap-y-3'
                            "
                        >
                            <template v-for="(user, i) in rankedUsers">
                                <div
                                    class="flex items-center col-span-2 md:col-span-1 rounded-s-full pl-2 sm:pl-4 overflow-hidden"
                                    :class="
                                        (user.email ===
                                        $page.props.auth.user?.email
                                            ? 'bg-purple-800'
                                            : '',
                                        i < 3 ? 'text-verde' : '')
                                    "
                                >
                                    <span class="mr-2"
                                        >{{ user.posicao }}º</span
                                    >
                                    {{ user.email }}
                                </div>
                                <div
                                    class="flex items-center justify-center text-center"
                                    :class="
                                        (user.email ===
                                        $page.props.auth.user?.email
                                            ? 'bg-purple-800'
                                            : '',
                                        i < 3 ? 'text-verde' : '')
                                    "
                                >
                                    {{ user.distancia }}M
                                </div>
                                <div
                                    class="flex items-center justify-center rounded-r-full"
                                    :class="
                                        (user.email ===
                                        $page.props.auth.user?.email
                                            ? 'bg-purple-800'
                                            : '',
                                        i < 3 ? 'text-verde' : '')
                                    "
                                >
                                    Premio
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            <div class="xl:hidden lg:mt-20 col-span-3 xl:col-span-1 flex justify-center">
                <img
                    :src="DinoTrofeu"
                    alt="dinoLogo"
                    class="max-w-[210px]  mt-auto absolute bottom-0"
                />
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from "vue";
import Background from "../../../../storage/imgs/home-page/bg4-desk.jpg";
import bgMobile from "../../../../storage/imgs/home-page/bg4-mobile.jpg";
import DinoTrofeu from "../../../../storage/imgs/home-page/dino-trofeu.svg";

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
</script>
