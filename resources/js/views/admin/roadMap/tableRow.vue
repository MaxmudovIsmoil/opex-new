<template>
    <tr :data-id="roadMapChild.id">
        <td class="text-center">{{ roadMapChild.stage }}</td>
        <td>{{ roadMapChild.instanceName }}</td>
        <td>{{ roadMapChild.users }}</td>
        <td>
            <div class="d-flex justify-content-end">
                <button class="btn btn-outline-primary btn-sm me-2"        
                    title="Edit"
                    @click="$emit('edit')"
                >
                    <font-awesome-icon :icon="['fas', 'pen']" />
                </button>
                <button class="btn btn-outline-danger btn-sm" 
                    href="javascript:void(0);"
                    @click="$emit('delete')"
                    title="Delete"
                >
                    <font-awesome-icon :icon="['fas', 'trash']" />
                </button>
            </div>
        </td>
    </tr>
</template>

<script>
export default {
    name: 'TableRow',
    props: {
        roadMapChild: {
            type: Object,
            required: true
        }
    },
    emits: ['edit', 'delete']
}
</script>
