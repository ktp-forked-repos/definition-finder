<?hh // strict
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
use namespace HH\Lib\{C, Str, Vec};

function properties_from_ast(
  ConsumerContext $context,
  HHAST\PropertyDeclaration $outer,
): vec<ScannedProperty> {
  $attributes = attributes_from_ast($outer->getAttributeSpec());
  $doc = doccomment_from_ast($context['definitionContext'], $outer);

  $modifiers = $outer->getModifiers();
  if ($modifiers instanceof HHAST\EditableList) {
    $modifiers = _Private\items_of_type($modifiers, HHAST\EditableToken::class);
  } else {
    $modifiers = vec[$modifiers];
  }
  $has_modifier = (classname<HHAST\EditableToken> $class) ==>
    C\any($modifiers, $m ==> $m instanceof $class);
  $is_static = $has_modifier(HHAST\StaticToken::class)
    ? StaticityToken::IS_STATIC
    : StaticityToken::NOT_STATIC;
  $visibility = $has_modifier(HHAST\PrivateToken::class)
    ? VisibilityToken::T_PRIVATE
    : (
        $has_modifier(HHAST\ProtectedToken::class)
          ? VisibilityToken::T_PROTECTED
          : VisibilityToken::T_PUBLIC
      );
  $type = typehint_from_ast($context, $outer->getType());

  return Vec\map(
    _Private\items_of_type(
      $outer->getDeclarators(),
      HHAST\PropertyDeclarator::class,
    ),
    $inner ==> new ScannedProperty(
      $inner,
      Str\strip_prefix($inner->getName()->getText(), '$'),
      context_with_node_position($context, $inner)['definitionContext'],
      $attributes,
      $doc,
      $type,
      $visibility,
      $is_static,
      value_from_ast($inner->getInitializer()?->getValue()),
    ),
  );
}
