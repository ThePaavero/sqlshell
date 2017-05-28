<template>
  <div>
    <form method='post' action='#' class='login-form' @submit.prevent='$parent.sendPassword(this.password)'>
      <p>
        <label>Log in: <input type='password' name='password' required v-model='password'/></label>
        <input type='submit' value='Log in'/>
      </p>
    </form>
  </div>
</template>

<script>
  export default{
    props: ['sendPasswordCallback'],
    data() {
      return {
        password: null
      }
    },
    methods: {}
  }
</script>

<style lang='scss' rel='stylesheet/scss' scoped>
</style>
