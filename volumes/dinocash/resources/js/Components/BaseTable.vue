<template>
    <div class="overflow-x-auto border-gray-400 border-[1px] rounded-lg h-fit max-h-[71%]">
        <table
            class="table bg-[#212121] text-white border-separate border-tools-table-outline"
        >
            <thead>
                <tr class="text-white font-extrabold uppercase text-lg">
                    <th v-for="{ label } in columns" :key="label">
                        {{ label }}
                    </th>
                    <th v-if="!hideActions">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr
                    class="border-b-0"
                    v-for="(item, index) in rows"
                    :key="index"
                >
                    <slot
                        v-for="({ key }, index) in columns"
                        :value="item[key]"
                        :name="key"
                        :key="index"
                        :item="item"
                    >
                        <td class="pb-0 pt-0">
                            <span>{{ item[key] }}</span>
                        </td>
                    </slot>
                    <slot name="actions" :value="item"> </slot>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script setup lang="ts">
import { defineProps, defineEmits, ref, computed, watch } from "vue";
const tableSelect = ref(false);
const selecteds = ref<Row[]>([]);
const props = defineProps({
    columns: {
        type: Array as () => { key: string; label: string }[],
        required: true,
    },
    rows: {
        type: Array as () => Row[],
        required: true,
    },
    hideActions: {
        type: Boolean,
        default: false,
    },
    select: {
        type: Boolean,
        default: true,
    },
    title: {
        type: String,
        default: null,
    },
});
interface Row {
    [key: string]: any;
}
const selectedIds = computed(() => selecteds.value.map((item) => item.id));
function togleAll() {
    if (tableSelect.value) {
        const payload = [...props.rows];
        selecteds.value = payload;
    } else {
        selecteds.value = [];
    }
}
function toggleSelection(item: Row) {
    const index = selectedIds.value.indexOf(item.id);
    if (index === -1) {
        const newArray = [...selecteds.value, item];
        selecteds.value = newArray;
    } else {
        const newArray = [...selecteds.value];
        newArray.splice(index, 1);
        selecteds.value = newArray;
    }
}
watch(
    () => selecteds.value,
    (value) => {
        if (value.length === props.rows.length) {
            tableSelect.value = true;
        } else {
            tableSelect.value = false;
        }
    }
);

const emit = defineEmits(["new", "edit", "delete", "update:selecteds"]);
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
