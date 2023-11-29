<template>
    <div>
        <div class="text-white font-bold uppercase text-sm">
            {{ label }}
        </div>
        <div
            class="rounded-md border-[1px] border-gray-500 bg-[#212121] w-full text-white px-3 py-2 font-bold"
        >
            <span
                class="mr-2"
                :class="negative ? 'text-red-600' : 'text-green-500 '"
            >
                $
            </span>
            <span class="text-center">
                {{ toBRL(value) }}
            </span>
        </div>
    </div>
</template>
<script setup lang="ts">
import { defineProps } from "vue";
const { label, value, negative } = defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
    negative: {
        type: Boolean,
        default: false,
    },
});
const toBRL = (value) => {
    return Number(value).toLocaleString("pt-br", {
        style: "currency",
        currency: "BRL",
    });
};
</script>
