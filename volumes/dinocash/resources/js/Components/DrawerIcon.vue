<template>
    <img class="mr-2" :src="source" />
</template>

<script setup lang="ts">
import { ref, defineProps, onMounted } from "vue";

const { src } = defineProps(["src"]);
const source = ref("");

async function getImage() {
    const image = await import(`/storage/imgs/user/icons/${src}`);
    source.value = image;
}
onMounted(async () => {
    await getImage();
});
</script>
