<template>
  <tr class="fw-semibold tr">
      <td class="align-middle">
          <p class="text-center mb-0">
              {{ index }} <br />
              <router-link  class="text-primary btn btn-sm pt-0" to="/order-detail"
                  style="font-size: 18px">
                  <font-awesome-icon :icon="['fas', 'eye']" />
          </router-link>
          </p>
      </td>
      <td class="align-middle">{{ order.type }}</td>
      <td class="align-middle">{{ order.email }}</td>
      <td class="align-middle">{{ order.secondaryRecipients }}</td>
      <td class="align-middle">{{ order.status }}</td>
      <td class="align-middle">{{ order.currentInstance }}</td>
      <td class="align-middle text-center">{{ order.stage }}</td>
      <td class="align-middle">{{ order.deadline }}</td>
      <td class="align-middle">{{ order.comment }}</td>
      <td class="align-middle">{{ order.created }}</td>
  </tr>
</template>

<script>
export default{
    props:{
        order:{
            type:Object,
            required:true
        },
        index:{
            type:Number
        }
    }
}
</script>