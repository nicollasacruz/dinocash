<script setup lang="ts">
import AffiliateLayout from "@/Layouts/AffiliateLayout.vue";
import { Head } from "@inertiajs/vue3";
import { ref } from "vue";
import CurrencyBox from "@/Components/CurrencyBox.vue";
import TextBox from "@/Components/TextBox.vue";
import { UserIcon } from "@heroicons/vue/24/solid";

const showModal = ref(false);

const selectedUser = ref(null);
function selectUser(user) {
  showModal.value = true;
  selectedUser.value = user;
}
</script>

<template>
  <Head title="Dashboard" />

  <AffiliateLayout>
    <div class="flex justify-between">
      <div class="text-4xl text-white font-bold mb-5">Dashboard</div>
      <div class="flex gap-x-5 -mt-4">
        <TextBox
          label="Online"
          :value="2"
          label-text="text-green-500"
        >
          <template #icon>
            <UserIcon class="w-5 fill-green-500" />
          </template>
        </TextBox>
        <TextBox label="Cadastros" :value="2">
          <template #icon>
            <UserIcon class="w-5" />
          </template>
        </TextBox>
      </div>
    </div>             

    <div class="grid xs:  grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-x-2 mt-4">
      <CurrencyBox label="Lucro em 30 dias" :value="2" />
      <CurrencyBox label="Prejuizo em 30 dias" :value="2" negative />
      <CurrencyBox label="Lucro Total" :value="2" />
      <CurrencyBox label="Prejuizo Total" :value="2" negative />
      <CurrencyBox label="Lucro do dia" :value="2" />
    </div>
    <div class="text-2xl font-bold text-white mt-6 mb-2">Últimos Cadastros</div>
  </AffiliateLayout>
</template>
