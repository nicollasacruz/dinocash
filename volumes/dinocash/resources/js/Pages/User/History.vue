<template>
  <UserLayouyt>
    <div class="p-2 lg:px-8">
      <div class="text-center uppercase text-3xl text-gray-800 mb-4">
        Histórico
      </div>
      <div class="w-full flex-col flex gap-y-4 text-gray-800">
        <div class="grid grid-cols-4 text-[11px] lg:text-xl text-center">
          <div>Data</div>
          <div>Distancia</div>
          <div>Resultado</div>
          <div>Valor</div>
        </div>
        <div
          v-for="({ updated_at, distance, type, finalAmount }, i) in listTransactions"
          class="grid grid-cols-4 bg-gray-200 py-2 text-sm lg:text-xl text-center"
        >
          <div>
            {{ dayjs(updated_at).format("DD/MM/YYYY") }}
          </div>
          <div>
            {{ distance }} M
          </div>
          <div :class="type === 'win' ? 'text-green-500' : 'text-red-500'">
            {{ type === 'win' ? 'GANHOU' : 'PERDEU' }}
          </div>
          <div>
            {{ toBRL(finalAmount) }}
          </div>
        </div>
      </div>
    </div>  
  </UserLayouyt>
</template>
<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import dayjs from "dayjs";

const { transactions } = defineProps(['transactions', ]);
const listTransactions = transactions.reverse();
function toBRL(value) {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value);
}
</script>
