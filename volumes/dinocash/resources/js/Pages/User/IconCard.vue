<template>
    <div>
        <img :src="src" class="w-24 sm:w-32 mx-auto" />
        <div class="text-xs sm:text-sm text-center">
            Raridade:
            <b>
                {{ raridade }}
            </b>
        </div>
        <div class="font-extrabold text-center -mt-1 text-white" v-if="preco">
            {{
                Number(preco).toLocaleString("pt-br", {
                    style: "currency",
                    currency: "BRL",
                })
            }}
        </div>
    </div>
</template>
<script setup lang="ts">
import { defineProps } from "vue";
import icon1 from "../../../../storage/imgs/user/icons/icon1.png";
defineProps({
    src: {
        type: String,
        default: icon1,
    },
    raridade: {
        type: String,
        default: "Comum",
    },
    preco: {
        type: Number,
    },
});
</script>
