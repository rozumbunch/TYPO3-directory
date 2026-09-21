import FormEngineValidation from '@typo3/backend/form-engine-validation.js';

class FormEngineEvaluation {
  static bound = false;

  static registerCustomEvaluation(name) {
    FormEngineValidation.registerCustomEvaluation(name, FormEngineEvaluation.evaluateCoordinate);
    FormEngineEvaluation.bindValidation();
  }

  static bindValidation() {
    if (FormEngineEvaluation.bound) {
      return;
    }
    FormEngineEvaluation.bound = true;

    document.addEventListener('t3-formengine-postfieldvalidation', (event) => {
      const field = event.detail?.field;
      if (!field || !FormEngineEvaluation.isCoordinateField(field)) {
        return;
      }
      FormEngineEvaluation.validateCoordinateField(field);
    });
  }

  static isCoordinateField(field) {
    const rules = field.dataset.formengineValidationRules;
    if (!rules) {
      return false;
    }

    try {
      return JSON.parse(rules).some((rule) => String(rule.type).includes('CoordinateEvaluation'));
    } catch {
      return false;
    }
  }

  static getRange(field) {
    const name = field.dataset.formengineInputName || field.getAttribute('name') || '';
    if (name.includes('[latitude]')) {
      return { min: -90, max: 90 };
    }
    if (name.includes('[longitude]')) {
      return { min: -180, max: 180 };
    }

    return { min: -180, max: 180 };
  }

  static isValidCoordinate(value, min, max) {
    const trimmed = String(value).trim();
    if (trimmed === '') {
      return true;
    }

    if (/^-?\d+[.,]\d+\s*,\s*-?\d+[.,]\d+$/.test(trimmed)) {
      return false;
    }

    let normalized = trimmed;
    if (/^-?\d+,\d+$/.test(trimmed)) {
      normalized = trimmed.replace(',', '.');
    }

    if (!/^-?\d+(\.\d{1,8})?$/.test(normalized)) {
      return false;
    }

    const number = Number(normalized);

    return Number.isFinite(number) && number >= min && number <= max;
  }

  static validateCoordinateField(field) {
    const range = FormEngineEvaluation.getRange(field);
    const valid = FormEngineEvaluation.isValidCoordinate(field.value || '', range.min, range.max);
    field.classList.toggle('has-error', !valid);
    field.setAttribute('aria-invalid', (!valid).toString());
    field.closest('.t3js-formengine-validation-marker')
      ?.querySelector('.t3js-formengine-label')
      ?.classList.toggle('has-error', !valid);

    try {
      FormEngineValidation.markParentTab(field, valid);
    } catch {
      // FormEngine tab marking is unavailable during early init.
    }

    return valid;
  }

  static evaluateCoordinate(value) {
    const trimmed = String(value).trim();
    if (trimmed === '') {
      return '';
    }

    if (/^-?\d+[.,]\d+\s*,\s*-?\d+[.,]\d+$/.test(trimmed)) {
      return trimmed;
    }

    let normalized = trimmed;
    if (/^-?\d+,\d+$/.test(trimmed)) {
      normalized = trimmed.replace(',', '.');
    }

    if (!/^-?\d+(\.\d{1,8})?$/.test(normalized)) {
      return trimmed;
    }

    return normalized;
  }
}

export { FormEngineEvaluation };
