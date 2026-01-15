import './bootstrap';
import TomSelect from 'tom-select';
import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.css";

// 日本語化する場合
import { Japanese } from "flatpickr/dist/l10n/ja.js";

flatpickr(".js-flatpickr", {
  locale: Japanese,
  dateFormat: "Y/m/d",
});

// TomSelectをグローバルに公開（Bladeコンポーネントから使用）
window.TomSelect = TomSelect;
