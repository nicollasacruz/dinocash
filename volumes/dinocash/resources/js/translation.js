export default {
  methods: {
    /**
     * Translate the given key.
     */
    __(key, replace = {}) {
      const keys = key.split('.');
      let translation = this.$page.props.language;

      keys.forEach(function (keyTmp) {
        translation = translation[keyTmp]
          ? translation[keyTmp]
          : keyTmp;
      });

      Object.keys(replace).forEach(function (key) {
        translation = translation.replace(':' + key, replace[key]);
      });

      return translation;
    },

    /**
     * Translate the given key with basic pluralization.
     */
    __n(key, number, replace = {}) {
      const options = key.split('|');

      key = options[1];
      if (number == 1) {
        key = options[0];
      }

      return this.__(key, replace);
    },
  },
};
