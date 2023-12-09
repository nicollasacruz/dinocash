<template>
  <Head title="Movimentações" />

  <UserLayouyt>
    <div class="p-2 lg:px-8 overflow-hidden flex flex-col h-full">
      <div class="text-center uppercase text-3xl text-gray-800 mb-4">
        Movimentação
      </div>
      <div
        class="grid grid-cols-4 text-[11px] lg:text-xl text-center text-gray-800"
      >
        <div>Data</div>
        <div>Movimentação</div>
        <div>Status</div>
        <div>Valor</div>
      </div>
      <div class="overflow-auto flex flex-col gap-y-2">
        <div
          v-for="(
            { updated_at, transaction_type, type, amount }, i
          ) in transactions"
          class="grid grid-cols-4 bg-gray-200 py-2 text-sm lg:text-xl text-center"
        >
          <div>
            {{ dayjs(updated_at).format("DD/MM/YYYY") }}
          </div>
          <div>
            {{ transaction_type === "deposit" ? "DEPÓSITO" : "SAQUE" }}
          </div>
          <div
            :class="
              type === 'pending'
                ? 'text-yellow-500'
                : type === 'rejected'
                ? 'text-red-500'
                : 'text-green-500'
            "
          >
            {{
              type === "pending"
                ? "PENDENTE"
                : type === "rejected"
                ? "REJEITADO"
                : "FINALIZADO"
            }}
          </div>
          <div>
            {{ toBRL(amount) }}
          </div>
        </div>
      </div>
    </div>
  </UserLayouyt>
</template>
<script setup lang="ts">
import UserLayouyt from "../..//Layouts/UserLayout.vue";
import dayjs from "dayjs";

const { transactions } = defineProps(["transactions"]);
// const transactions = [];
function toBRL(value) {
  return new Intl.NumberFormat("pt-BR", {
    style: "currency",
    currency: "BRL",
  }).format(value);
}
</script>

<style scoped>
.overflow-x-auto {
  scrollbar-width: thin; /* Para navegadores que suportam a propriedade scrollbar-width */
  scrollbar-color: #555555 #212121; /* Cor da barra de rolagem e cor do fundo da barra de rolagem */
}

.overflow-x-auto::-webkit-scrollbar {
  width: 8px; /* Largura da barra de rolagem no Chrome/Safari/Edge */
}

.overflow-x-auto::-webkit-scrollbar-thumb {
  background-color: #555555; /* Cor da barra de rolagem */
  border-radius: 4px; /* Borda arredondada da barra de rolagem */
}

.overflow-x-auto::-webkit-scrollbar-track {
  background-color: #212121; /* Cor do fundo da barra de rolagem */
}
</style>
