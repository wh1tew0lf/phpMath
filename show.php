<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
            $res = unserialize(file_get_contents('./finish.txt'));
            $cnt = count($res);
            $keys = array_keys(reset($res));
        ?>
        <table cellpadding="1" cellspacing="1" border="1">
            <?php foreach ($keys as $key): ?>
                <tr>
                    <th><?php echo $key; ?></th>
                    <?php for($i = 0; $i < $cnt; ++$i): ?>
                        <?php if (is_array($res[$i][$key])): ?>
                            <td>
                                <?php foreach($res[$i][$key] as $k => $v): ?>
                                    <?php echo $k . '&nbsp;' . $v; ?><br />
                                <?php endforeach; ?>
                            </td>
                        <?php else: ?>
                            <td><?php echo $res[$i][$key]; ?></td>
                        <?php endif; ?>
                    <?php endfor; ?>
                </tr>
            <?php endforeach; ?>
        </table>
    </body>
</html>
