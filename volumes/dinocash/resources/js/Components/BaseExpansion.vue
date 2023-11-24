<template>
    <div
        :class="[isOpen ? 'collapse-open' : 'collapse-close']"
        @click="isOpen = !isOpen"
        class="collapse collapse-arrow border-[#3B2B45] text-[#3B2B45] border-8 bg-white font-menu cursor-pointer w-full md:w-[600px]"
    >
        <div class="collapse-title text-3xl font-medium py-2">
            {{ title }}
        </div>
        <div class="collapse-content -mt-3">
            <hr />
            <p class="mt-2">
                {{ content }}
            </p>
        </div>
    </div>
</template>
<script setup>
import { ref } from "vue";
const isOpen = ref(false);
const { title, content } = defineProps({
    title: {
        type: String,
        required: true,
    },
    content: {
        type: String,
        required: true,
    },
});
</script>
