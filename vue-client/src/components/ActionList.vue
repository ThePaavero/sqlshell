<template>
  <div>
    <div class='in-grid'>
      <div class='prompt-help'>
        <small @click.prevent='dispatch("run-query")'>CTRL + Enter to run query</small>
        <small @click.prevent='dispatch("toggle-tables")'>CTRL + B to toggle list of tables</small>
        <small @click.prevent='dispatch("toggle-favorites")'>CTRL + L to toggle list of favorite queries</small>
        <small @click.prevent='dispatch("save-query-to-favorites")'>CTRL + S to save query to favorites</small>
        <small @click.prevent='dispatch("delete-last-favorite")'>CTRL + D to delete the last favorite</small>
        <small @click.prevent='dispatch("download-dump")'>CTRL + E to download a dump</small>
        <small @click.prevent='dispatch("focus-on-prompt")'>ESC to focus on prompt</small>
      </div><!-- prompt-help -->
    </div><!-- in-grid -->
  </div>
</template>

<script>
  export default{
    props: [],
    data() {
      return {}
    },
    methods: {
      dispatch(actionKey) {
        this.$parent.dispatch(actionKey)
      }
    }
  }
</script>

<style lang='scss' rel='stylesheet/scss' scoped>
</style>
