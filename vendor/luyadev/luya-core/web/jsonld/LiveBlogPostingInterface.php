<?php
namespace luya\web\jsonld;

/**
 * JsonLd - Live Blog Posting Interface
 *
 * @see http://schema.org/LiveBlogPosting
 *
 * @author Alex Schmid <alex.schmid@stud.unibas.ch>
 * @since 1.0.1
 */
interface LiveBlogPostingInterface extends BlogPostingInterface
{
    /**
     * @return string
     */
    public function getCoverageEndTime();
    /**
     * @param DateTimeValue $coverageEndTime
     * @return LiveBlogPosting|LiveBlogPostingTrait
     */
    public function setCoverageEndTime(DateTimeValue $coverageEndTime);

    /**
     * @return string
     */
    public function getCoverageStartTime();

    /**
     * @param DateTimeValue $coverageStartTime
     * @return LiveBlogPosting|LiveBlogPostingTrait
     */
    public function setCoverageStartTime(DateTimeValue $coverageStartTime);

    /**
     * @return BlogPosting
     */
    public function getLiveBlogUpdate();

    /**
     * @param BlogPosting $liveBlogUpdate
     * @return LiveBlogPosting|LiveBlogPostingTrait
     */
    public function setLiveBlogUpdate(BlogPosting $liveBlogUpdate);
}