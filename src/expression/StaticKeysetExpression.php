<?hh // strict
/*
 *  Copyright (c) 2015-present, Facebook, Inc.
 *  All rights reserved.
 *
 *  This source code is licensed under the MIT license found in the
 *  LICENSE file in the root directory of this source tree.
 *
 */

namespace Facebook\DefinitionFinder\Expression;

use namespace Facebook\HHAST;

final class StaticKeysetExpression extends Expression<keyset<arraykey>> {
  const type TNode = HHAST\VectorIntrinsicExpression;

  <<__Override>>
  protected static function matchImpl(
    this::TNode $node,
  ): ?Expression<keyset<arraykey>> {
    $m = $node->getMembers();
    if ($m === null) {
      return new self(keyset[]);
    }
    $in = StaticListExpression::match($m)?->getValue();
    if ($in === null) {
      return new self(keyset[]);
    }

    $members = keyset[];
    foreach ($in as $item) {
      if (is_int($item)) {
        $members[] = $item;
        continue;
      }
      if (is_string($item)) {
        $members[] = $item;
        continue;
      }
      return null;
    }
    return new self($members);
  }
}
