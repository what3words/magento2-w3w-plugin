<?php
/**
 * What3Words_What3Words
 *
 * @category    Models
 * @copyright   Copyright (c) 2024 What3Words
 * @author      what3words Developer <developer@what3words.com>
 * @link        http://www.what3words.com
 */

namespace What3Words\What3Words\Model\Config\Comment;

use Magento\Config\Model\Config\CommentInterface;

class SaveCoordinates implements CommentInterface
{
    public function getCommentText($elementValue)
    {
        return "<strong>NOTE:</strong> This feature won't work if you're on a free plan or have exceeded your quota. Check the console and network panel for errors. No coordinates will be saved. Please review our <a href=\"https://accounts.what3words.com/select-plan\">plans</a> for higher allowances.";
    }
}
