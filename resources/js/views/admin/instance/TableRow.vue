<template>
    <tr class="js_this_tr" data-id="{{ instance.id }}">
        <td class="text-center">{{ index }}</td>
        <td>
            {{ instance.name_en }}
        </td>
        <td>
            {{ instance.name_ru }}
        </td>
        <td class="text-center">{{ instance.time_line }}</td>
        <td class="text-end">
            <div class="d-flex" style="float: right;">
                <a class="btn btn-outline-primary btn-sm me-2 js_edit_btn" 
                    data-bs-toggle="modal" 
                    data-bs-target="#customModal"
                    title="Edit">
                    <font-awesome-icon :icon="['fas', 'pen']"/>
                </a>
                <a class="btn btn-outline-danger btn-sm js_delete_btn" 
                    data-bs-toggle="modal"
                    data-bs-target="#deleteModal"
                    title="Delete">
                    <font-awesome-icon :icon="['fas', 'trash-alt']" />
                </a>
            </div>
        </td>
    </tr>
</template>
<script>
export default {
  props: {
    instance: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        required: true
    }
  },
};
</script>
