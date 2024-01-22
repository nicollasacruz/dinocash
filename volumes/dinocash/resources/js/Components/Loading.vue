<template>
    <Teleport v-if="loading" to="#app">
        <div
            class="h-screen z-10 w-screen absolute bg-slate-600 opacity-50 flex justify-center align-center"
        >
            <span class="loading loading-spinner loading-md"></span>
        </div>
    </Teleport>
</template>
<script setup lang="ts">
import { defineProps } from "vue";

const props = defineProps({
    loading: {
        type: Boolean,
        default: false,
    },
});
</script>
