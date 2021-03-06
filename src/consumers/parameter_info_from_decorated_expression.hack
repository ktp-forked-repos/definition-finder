/*
 *  Copyright (c) 2015-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\DefinitionFinder;

use namespace Facebook\HHAST;

function parameter_info_from_decorated_expression(
  HHAST\DecoratedExpression $de,
): shape('name' => HHAST\VariableToken, 'byref' => bool, 'variadic' => bool) {
  $inner = $de->getExpression();
  if ($inner instanceof HHAST\DecoratedExpression) {
    $ret = parameter_info_from_decorated_expression($inner);
  } else {
    invariant(
      $inner instanceof HHAST\VariableToken,
      "Don't know how to handle %s ('%s')",
      \get_class($inner),
      $inner->getCode(),
    );
    $ret = shape(
      'name' => $inner,
      'byref' => false,
      'variadic' => false,
    );
  }

  $d = $de->getDecorator();
  if ($d instanceof HHAST\DotDotDotToken) {
    $ret['variadic'] = true;
  } else if ($d instanceof HHAST\AmpersandToken) {
    $ret['byref'] = true;
  } else {
    invariant_violation(
      "Unhandled decorator: %s ('%s')",
      \get_class($d),
      $de->getCode(),
    );
  }

  return $ret;
}
