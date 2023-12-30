<template>
  <div className="join" v-for="link in data.links">
    <template v-if="link.url !== null || link.label == '...'">
      <Link
        className="join-item btn "
        :href="link.url"
        v-html="link.label"
        :className="{
          'btn-active': link.active,
          'disabled': !!link.url
        }"
      />
    </template>
  </div>
</template>

<script setup>
import { ChevronLeftIcon, ChevronRightIcon } from "@heroicons/vue/20/solid";
import { Link } from "@inertiajs/vue3";

defineProps({
  data: Object,
});
</script>