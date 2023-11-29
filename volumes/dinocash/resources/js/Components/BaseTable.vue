<template>
    <div class="overflow-x-auto">
        <table
            class="table bg-[#212121] text-white border-gray-400 border-[1px] border-separate border-tools-table-outline rounded-lg"
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
