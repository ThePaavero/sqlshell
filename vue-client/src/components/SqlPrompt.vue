<template>
  <div>
    <textarea @keyup='updateStore' v-model='queryString'></textarea>
  </div>
</template>

<script>
  export default{
    props: ['defaultQueryString'],
    data() {
      return {
        queryString: this.defaultQueryString
      }
    },
    methods: {
      updateStore() {
        this.$store.commit('setCurrentQueryString', this.queryString)
      }
    }
  }
</script>

<style lang='scss' rel='stylesheet/scss' scoped>
</style>
