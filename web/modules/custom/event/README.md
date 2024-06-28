# Event module

## Features

### Creation of a block related the X latest event content type (2h30)

The best option for this feature is to create a block plugin with a contextual definition on node.
This gives me direct access to the node on which the block is displayed.
From there, I need to retrieve the event category, so I figure it'll have some business logic. So I create a bundle class for my event content.
As I'm going to have to query to retrieve some nodes, I'm also going to create a class to group the events.
Finally, I think we're going to post a card after the content, so I'm returning nodes in teaser format if exists.

To apply the new section into my event node, I use the hook `hook_preprocess_node__bundle` to create instance of the block.

### Unpublished the event with event date exceed with queue worker plugin (1h20)

#### My thoughts

Multiple options are possible:

1. Use the cron to fill the queue and consume it
2. Use a drush command to fill the queue and consume it by `drush queue:run` command via cron tab

Then, there is two different options to consume the queue:

1. The normal way, one by one entity
2. The batch way, all entities to update on the same time

#### My pick

Given the amount of data to be processed each day and the fact that an event almost always takes place over a full day, I decided to use the end-to-end cron system.
In this way, the cron could pass by at least every day to carry out its tasks.

In this case, the batch method wouldn't make much sense, as the task is very fast, based on multiple data and there isn't enough data each day.
Moreover, the data is not sensitive, so there's no need for a supervisor to manage the queue in real time.

After that, it's also a matter of making collective decisions to get the best option.

### SAST (30 min)

I use an external project to apply all my sast tool (phpstan, rector, phpcs, ecs, phpmd, ...).

### Documentation (30 min)

