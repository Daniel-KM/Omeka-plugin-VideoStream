<?php
/**
 * @var Omeka_View $this
 * @var Item $item
 * @var string $video_filename
 */

$elementIds = json_decode(get_option('videostream_elements_ids'), true);

$sequences = get_records(
    'Item',
    array(
        'collection' => $item->collection_id,
        'sort_field' => 'Streaming Video,Segment Start',
        'advanced' => array(
            array(
                'element_id' => $elementIds['Streaming Video:Segment Type'],
                'type' => 'is exactly',
                'terms' => 'Scene',
            ),
            array(
                'element_id' => $elementIds['Streaming Video:Video Filename'],
                'type' => 'is exactly',
                'terms' => $video_filename,
            ),
        ),
    ),
    null
);

if (!empty($sequences)):
    foreach ($sequences as $sequence):
        $segmentStart = metadata($sequence, array('Streaming Video', 'Segment Start'));
        $segmentEnd = metadata($sequence, array('Streaming Video', 'Segment End'));
        $segmentTitle = metadata($sequence, array('Dublin Core', 'Title'));
        $segmentDescription = metadata($sequence, array('Dublin Core', 'Description'));
    ?>
<div class="scene" id="<?php echo $segmentStart; ?>" title="<?php echo $segmentEnd; ?>" style="display:none;">
    <h2><?php echo __('Current video segment:'); ?></h2>
    <h3><?php echo link_to_item($segmentTitle, array(), 'show', $sequence); ?></h3>
    <div style="overflow:auto; max-height:150px;">
        <p><?php echo $segmentDescription; ?></p>
    </div>
    <p><?php echo __('Segment: %s - %s', $segmentStart, $segmentEnd); ?></p>
</div>
    <?php
    endforeach;
    set_current_record('item', $item);
?>
<hr style="color:lt-gray;" />
<script type="text/javascript">
    function getElementsByClass(searchClass, domNode, tagName) {
        if (domNode == null) {
            domNode = document;
        }
        if (tagName == null) {
            tagName = '*';
        }
        var el = new Array();
        var tags = domNode.getElementsByTagName(tagName);
        var tcl = " " + searchClass + " ";
        for (i = 0, j = 0; i < tags.length; i++) {
            var test = " " + tags[i].className + " ";
            if (test.indexOf(tcl) != -1) {
                el[j++] = tags[i];
            }
        }
        return el;
    }

    jwplayer("jwplayer_plugin").onTime(function(event) {
        var ctime = "0:00:00";
        var scenes;
        var sel;

        ctime = getTimeString(jwplayer("jwplayer_plugin").getPosition());
        scenes = getElementsByClass("scene");
        for (i = 0; i < scenes.length; i++) {
            sel = scenes[i];
            if (sel.getAttribute('id') <= ctime && sel.getAttribute('title') >= ctime) {
                sel.style.display = 'block';
            } else {
                sel.style.display = 'none';
            }
        }
    });
</script>
<?php endif; ?>
